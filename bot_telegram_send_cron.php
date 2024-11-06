<?php
include_once '__config.php';
include_once 'bot_config.php';
$limit_second = 60;
$micro_now = microtime(true);
$stop_limit = $micro_now + $limit_second;
//echo "</br>".$micro_now."</br>".$stop_limit." - лимит</br></br>===========================</br></br>";

function multi_message($array_message) {
    global $telegram_bot_token; 
    global $website; 

    $multi = curl_multi_init();
    $handles = array();
    foreach($array_message as $message) {
        /* if ($option['chat_id'] == 5079442067) {
            file_put_contents(__DIR__.'/bot_telegram_send_cron_message.txt', json_encode($message, true));
        }  */   
        $ch = curl_init();
        unset($option);
        $option['chat_id'] = $message['chat_id'];
        if (in_array($message['types'], ['sendMessage', 'copyMessage', 'sendPhoto', 'sendAudio', 'sendDocument', 'sendVideo', 'sendAnimation', 'sendVoice', 'editMessageText', 'editMessageCaption']) AND !$message['entities']) {$option['parse_mode'] = "HTML";}//все теги из html
        if (in_array($message['types'], ['sendChatAction'])) {
            $option['action'] = $message['text'];
        } else if (in_array($message['types'], ['sendSticker'])) {
            $option['sticker'] = $message['file'];
        } else if (in_array($message['types'], ['answerCallbackQuery'])) {
            $option['text'] = text($message['text'], 'bot');
            $option['callback_query_id'] = $message['edit_message_id'];
            if ($message['keyboard']) {$option['show_alert'] = True; unset($message['keyboard']);}            
        } else if (in_array($message['types'], ['sendMessage'])) {
            if ($message['edit_message_id']) {
                $message['types'] = "editMessageText";
                $option['message_id'] = $message['edit_message_id'];
            }
            $option['text'] = text($message['text'], 'bot');
            if ($message['entities']) {$option['entities'] = json_encode(text($message['entities'], 'web'), true);}
            if ($message['preview']) {$option['disable_web_page_preview'] = false;} else {$option['disable_web_page_preview'] = true;} //выключен показ ссылок картинкой сайта
        } else if (in_array($message['types'], ['copyMessage', 'sendPhoto', 'sendAudio', 'sendDocument', 'sendVideo', 'sendAnimation', 'sendVoice', 'editMessageCaption'])) {
            if ($message['edit_message_id']) {
                $message['types'] = "editMessageMedia";
                $option['message_id'] = $message['edit_message_id'];
            }
            $option['caption'] = text($message['text'], 'bot');
            if ($message['types'] == 'sendPhoto') {$option['photo'] = $message['file'];}
            else if ($message['types'] == 'sendVideo') {$option['video'] = $message['file'];}
            else if ($message['types'] == 'sendAnimation') {$option['animation'] = $message['file'];}
            else if ($message['types'] == 'sendLocation') {$option['latitude'] = explode(',', $message['file'])[0]; $option['longitude'] = explode(',', $message['file'])[1];}
            else if ($message['types'] == 'sendMediaGroup') {$option['media'] = $message['file'];}
            else if ($message['types'] == 'sendDocument') {$option['document'] = $message['file'];}
            if ($message['entities']) {$option['caption_entities'] = json_decode(text($message['entities'], 'web'), true);}
        }  
        if ($message['keyboard']) {
            $option['reply_markup'] = json_encode(["inline_keyboard" => json_decode(text($message['keyboard'], 'bot'), true)]);
        } else if ($message['menu']) {
            $option['reply_markup'] = json_encode(["keyboard" => json_decode(text($message['menu'], 'bot'), true), "resize_keyboard" => true]);
        }         
        if ($option['chat_id'] == 5079442067 AND !file_exists(__DIR__.'/bot_telegram_send_cron_option_5079442067.txt')) {
           file_put_contents(__DIR__.'/bot_telegram_send_cron_option_5079442067.txt', json_encode($option, true));
        } else if ($option['chat_id'] == 355590439 AND !file_exists(__DIR__.'/bot_telegram_send_cron_option_355590439.txt')) {
            file_put_contents(__DIR__.'/bot_telegram_send_cron_option_355590439.txt', json_encode($option, true));
        }
        $arr = [
            CURLOPT_URL => 'https://api.telegram.org/bot'.$telegram_bot_token.'/'.$message['types'],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $option,
        ]; 
        curl_setopt_array($ch, $arr);
        curl_multi_add_handle($multi, $ch);
        $handles[] = $ch;
    }
    //обработчик
    do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active && $mrc == CURLM_OK) {if (curl_multi_select($multi) == -1) {usleep(100);}do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);}
    //сохранение
    foreach($handles as $channel) {
        $z++;
        $answer = curl_multi_getcontent($channel);
        curl_multi_remove_handle($multi, $channel);
        //file_put_contents(__DIR__.'/__send2_answer.txt', $answer);
        $answer = json_decode($answer, true);
        $result[] = $answer;
    }
    return $result;
}


for ($i = 1; $i <= 10000; $i++) {
    $now_old = $now;
    $now = microtime(true);
    //echo 'заняло: '.($now - $now_old).' / '.$now_z1.'</br>';
    if ($now >= $stop_limit) {
        $echo = '</br></br>Успешное завершение по таймеру -  '.$limit_second." секунд";
        break;
    }
    //echo "|";
    $array_message = array();
    $array_chat_id = array();
    $result = array();
    $err_array = array();
    unset($count_mes_old, $max_row);
    $now_max = date('Y-m-d H:i:s', strtotime('now')); 
    $count_bot_message = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_message` WHERE `status` = '' AND `timer` <= '$now_max'"))[0];
    if ($count_bot_message > 301) {
        $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_message` WHERE `status` = '' AND `timer` <= '$now_max' ORDER BY `id` ASC LIMIT 200, 1");
        while($row = mysqli_fetch_assoc($sql) ){
            $max_row = $row['id'];
        }
    } else if ($count_bot_message > 0){
        $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_message` WHERE `status` = '' AND `timer` <= '$now_max' ORDER BY `id` DESC LIMIT 1");
        while($row = mysqli_fetch_assoc($sql) ){
            $max_row = $row['id'];
        }
    }
    //echo '</br>$max_row = '.$max_row.'</br>';
    if ($max_row) {
        mysqli_query($CONNECT, "UPDATE `bot_message` SET `status` = '$now' WHERE `status` = '' AND `id` <= $max_row  AND `timer` <= '$now_max' ");
        $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_message` WHERE `status` = '$now'");
        while($row = mysqli_fetch_assoc($sql) ){
            if (!in_array($row['chat_id'], $array_chat_id)) {
                $array_chat_id[] = $row['chat_id'];
                $array_message[] = $row;
                if (count($array_message) >= 15) {
                    mysqli_query($CONNECT, "UPDATE `bot_message` SET `status` = '' WHERE `status` = '$now' AND `id` > $row[id]");
                    break;
                }            
            } else {
                mysqli_query($CONNECT, "UPDATE `bot_message` SET `status` = '' WHERE `id` = $row[id]");
            }
        }
    }
    /* echo $now.' / '.$max_row.'<pre>';
    print_r($array_chat_id);
    echo '</pre></br>'; */
    
    $now_z1 = microtime(true);
    if (count($array_message)) {
        //file_put_contents(__DIR__.'/__array_message.txt', json_encode($array_message, true));
        $result = multi_message($array_message);
        //file_put_contents(__DIR__.'/__array_result.txt', json_encode($result, true));
        foreach($result as $key => $value) {
            unset($v);
            $v['chat_id'] = $array_message[$key]['chat_id'];
            $v['id'] = $array_message[$key]['id'];
            mysqli_query($CONNECT, "DELETE FROM `bot_message` WHERE `id` = '$v[id]'");

            if ($value['ok']) {// не объединять с $value['result']['message_id'] так как могуть быть без номера ответы, если это answerCallbackQuery
                if ($value['result']['message_id']) {
                    if (!$array_message[$key]['no_delete']) {
                        $v['types'] = $array_message[$key]['types'];
                        $v['message_id'] = $value['result']['message_id'];
                        mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`) VALUES ('$v[chat_id]', '$v[message_id]', '$v[types]')");
                    }
                    $count_mes_old[$v['chat_id']] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_message` WHERE `chat_id` = '$v[chat_id]'"))[0];
                    if (!$count_mes_old[$v['chat_id']]) {
                        $history_sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_history` WHERE `chat_id` = '$v[chat_id]' AND `old` = 1");
                        while($history = mysqli_fetch_assoc($history_sql) ){
                            mysqli_query($CONNECT, "DELETE FROM `bot_user_history` WHERE `id` = $history[id]");
                            $delete_answer = telegram("deleteMessage", ["chat_id" => $v['chat_id'], "message_id" => $history['message_id']]);
                            if ($delete_answer['description'] == "Bad Request: message can't be deleted for everyone") {//если сообщение старое и не удаляется, зачишаем на максимум как можем его
                                $edit_answer = telegram("editMessageText", ["chat_id" => $v['chat_id'], "message_id" => $history['message_id'], "text" => "..."]);
                                if ($edit_answer['description'] == "Bad Request: there is no text in the message to edit") {
                                    $answer2 = telegram("editMessageMedia", ["chat_id" => $v['chat_id'], "message_id" => $history['message_id'], "media" => $website."/bot_files/clean.jpg", "text" => ""]);    
                                }
                            }
                        }
                    }
                }
            } else if ($value['description'] == 'Forbidden: bot was blocked by the user'){
                $chat_id = $array_message[$key]['chat_id'];
                mysqli_query($CONNECT, "DELETE FROM `bot_user` WHERE `chat_id` = '$chat_id'");
                if ($module['dialog']) {
                    include_once __DIR__.'/bot_module_dialog/bot_dialog_stop.php';
                }
                unset($chat_id);
            } else {
                $err_array[] = [
                    'message' => $array_message[$key],
                    'answer' => $value,
                ];
            }
        }
        // проверка на ошибки
        if ($err_array) {
            if (!file_exists(__DIR__.'/bot_telegram_send_cron_error.txt')) {
                file_put_contents(__DIR__.'/bot_telegram_send_cron_error.txt', json_encode($err_array, true));
            }
        }
    } else {
        usleep(1000000 * 0.1);
    }
}
//echo "</br>===========================</br>Закончили в ".$now."</br>";
if ($echo) {
    //echo $echo;
} else {
    $echo = '</br></br>Не хватило на минуту '.$now; 
    file_put_contents(__DIR__.'/bot_telegram_send_cron_error.txt', 'не хватило повтора действий на минуту');  
}
   

// ============= удаляем застрявшие
$micro_now_very_old = microtime(true) - 100;
//echo '</br> ============= удаляем застрявшие </br></br>'.$micro_now_very_old.' = $micro_now_very_old</br>';
mysqli_query($CONNECT, "DELETE FROM `bot_message` WHERE `status` < '$micro_now_very_old' AND `status` != ''");

?>