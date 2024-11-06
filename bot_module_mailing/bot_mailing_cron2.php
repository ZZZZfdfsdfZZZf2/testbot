<?php
$back_dir = explode('/', __DIR__);;
array_pop($back_dir);
$back_dir = implode('/', $back_dir);
include_once $back_dir.'/__config.php';
include_once $back_dir.'/bot_config.php';
include_once __DIR__.'/bot_mailing_config.php';
if (!file_exists(__DIR__.'/bot_mailing_cron2.txt')) {file_put_contents(__DIR__.'/bot_mailing_cron2.txt', 'ok');}

if (!$admin_chat_id) {$admin_chat_id = array();}
if (!$worker) {$worker = array();}

$limit_second = 60;
$micro_now = microtime(true);
$stop_limit = $micro_now + $limit_second;

$now = date('Y-m-d H:i:s', strtotime('now')); 
$start = strtotime('now');
echo '</br> тип версии кода 05</br></br></br>';
echo 'Старт '.$now; 

function multi_parsing($type, $option, $array_chat_id) {
    global $telegram_bot_token; 

    if (in_array($type, ['sendMessage', 'copyMessage', 'sendPhoto', 'sendAudio', 'sendDocument', 'sendVideo', 'sendAnimation', 'sendVoice', 'editMessageText', 'editMessageCaption']) AND !$option['entities']) {$option['parse_mode'] = "HTML";}//все теги из html
	if (in_array($type, ['sendMessage', 'editMessageText']) AND !$option['preview']) {$option['disable_web_page_preview'] = true;} else {$option['disable_web_page_preview'] = false;} //выключен показ ссылок картинкой сайта
	unset($option['preview']);
	if ($option['keyboard']){
		$keyboard = json_encode(["inline_keyboard" => $option['keyboard']]);
		$option['reply_markup'] = $keyboard;
		unset($option['keyboard']);
	} else if ($option['menu']){
		$keyboard = json_encode(["keyboard" => $option['menu'], "resize_keyboard" => true]);
		$option['reply_markup'] = $keyboard;
		unset($option['menu']);
	}
	if ($type == 'sendMediaGroup'){
		$files = $option['media'];
		unset($option['media']);
		foreach($files as $key => $file) {
			if (mb_stripos($file, 'jpg') !== false OR mb_stripos($file, 'png') !== false) {$media_type = 'photo';}
			else if (mb_stripos($file, 'mp4') !== false) {$media_type = 'video';}
			if ($key == 0) {
				$media[0] = ["type" => $media_type, "media" => $file, "parse_mode" => "HTML"];
				if ($option['text']) { $media[0]["caption"] = $option['text']; }
			} else {
				$media[] = ["type" => $media_type, "media" => $file];
			}				
		}
		$option['media'] = json_encode($media);
		unset($option['text']);			
	} else if ($type == 'editMessageMedia'){
		if ($option['photo']) {$media_type = 'photo'; $option['media'] = $option['photo'];} 
		else if ($option['video']) {$media_type = 'video'; $option['media'] = $option['video'];}
		else if ($option['media']) {
			if (mb_stripos($option['media'], 'jpg') !== false OR mb_stripos($option['media'], 'png') !== false) {$media_type = 'photo';}
			else if (mb_stripos($option['media'], 'mp4') !== false) {$media_type = 'video';}
		}
		$option['media'] = json_encode(["type" => $media_type, "media" => $option['media'], "caption" => $option['text'], "parse_mode" => "HTML"]);
		unset($option['text'], $option['photo'], $option['video']);
	}
	if ($option['entities']) {	
		if ($option['caption']) {
			$option['caption_entities'] = json_encode($option['entities'], true);
			unset($option['entities']);
		} else {
			$option['entities'] = json_encode($option['entities'], true);
		}	
	}

    $multi = curl_multi_init();
    $handles = array();
    foreach($array_chat_id as $chat_id) {    
        $ch = curl_init();
        unset($option_full);
        $option_full = $option;
        $option_full['chat_id'] = $chat_id;
        $arr = [
            CURLOPT_URL => 'https://api.telegram.org/bot'.$telegram_bot_token.'/'.$type,
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT => 10, 
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $option_full,
        ]; 
        curl_setopt_array($ch, $arr);
        curl_multi_add_handle($multi, $ch);
        $handles[$chat_id] = $ch;
    }
    //обработчик
    do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active && $mrc == CURLM_OK) {if (curl_multi_select($multi) == -1) {usleep(100);}do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);}
    //сохранение
    foreach($handles as $channel) {
        $z++;
        $answer = curl_multi_getcontent($channel);
        curl_multi_remove_handle($multi, $channel);
        if (!file_exists(__DIR__.'/multiparsing_option.txt') AND !$answer) {file_put_contents(__DIR__.'/multiparsing_option.txt', json_encode($option_full, true));}
        //file_put_contents(__DIR__.'/multiparsing_answer.txt', $answer);
        $answer = json_decode($answer, true);
        $result[] = $answer;
    }
    return $result;
}


if ($telegram_bot_token) {
    $error_massive = array();
    for ($i = 1; $i <= 1000; $i++) { 
        if (microtime(true) >= $stop_limit) {
            echo '</br>Успешное завершение по таймеру</br>';
            break;
        }

        //for ($ii = 1; $ii <= 28; $ii++) {s
            $mailling_check = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_mailling` WHERE `status` = 'progress'"))[0];
            if ($mailling_check){
                unset($array_chat_id);
                unset($option);
                unset($user_id_last);
                $sql = mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `status` = 'progress' ORDER BY `id` ASC LIMIT 1");
                while($row = mysqli_fetch_assoc($sql) ){
                    $mailling = $row;
                }
                $array_chat_id = array();
                $option = array();

                // ==================== подгружаем 21 пользователя 

                $where = " WHERE `id` > '".$mailling['user_id_last']."' AND `id` <= '".$mailling['user_id_max']."' ";
                $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user` ".$where." ORDER BY `id` ASC LIMIT 21");
                while($row = mysqli_fetch_assoc($sql) ){
                    if (in_array($row['chat_id'], $worker)) {
                    } else if (in_array($row['chat_id'], $admin_chat_id) AND $row['chat_id'] != $mailling_check['admin_chat_id']) {
                    } else {
                        $array_chat_id[] = $row['chat_id'];
                    }
                    $user_id_last = $row['id'];
                }
                //echo '</br>$user_id_last = '.$user_id_last."</br>";
                if ($user_id_last > $mailling['user_id_last']) {
                    mysqli_query($CONNECT, "UPDATE `module_mailling` SET `user_id_last` = '$user_id_last' WHERE `id` = '$mailling[id]'");
                }/* 
                echo '</br>$array_chat_id = <pre>';
                print_r($array_chat_id);
                echo '</pre>'; */


                

                if (count($array_chat_id)){
                    echo '</br>Рассылаем</br>';              
                    if ($mailling['file_type'] AND $mailling['file_id']) {
                        if ($mailling['text']) {$option['caption'] = mailing_text($mailling['text'], 'bot');}
                        $option[$mailling['file_type']] = $mailling['file_id'];
                    } else {
                        if ($mailling['text']) {$option['text'] = mailing_text($mailling['text'], 'bot');}
                        $option['preview'] = $mailling['preview'];
                    }
                    if ($mailling['keyboard']) {$option['keyboard'] = json_decode(mailing_text($mailling['keyboard'], 'bot'), true);}
                    if ($mailling['entities']) {$option['entities'] = json_decode(mailing_text($mailling['entities'], 'bot'), true);}
                    /* echo '</br>$option = <pre>';
                    print_r($option);
                    echo '</pre>'; */

                    if ($option['photo']){ $array_answer = multi_parsing("sendPhoto", $option, $array_chat_id);} 
                    else if ($option['video']){ $array_answer = multi_parsing("sendVideo", $option, $array_chat_id); }
                    else if ($option['document']){ $array_answer = multi_parsing("sendDocument", $option, $array_chat_id); }
                    else if ($option['voice']){ $array_answer = multi_parsing("sendVoice", $option, $array_chat_id); }
                    else if ($option['video_note']){ $array_answer = multi_parsing("sendVideoNote", $option, $array_chat_id); }
                    else { $array_answer = multi_parsing("sendMessage", $option, $array_chat_id);}
                    /* echo '</br>$array_answer = <pre>';
                    print_r($array_answer);
                    echo '</pre>'; */
                    $z  = 0;
                    echo '</br>';
                    $retry_after = 0;
                    foreach($array_answer as $key => $answer) {
                        $user_count++;
                        $z++;
                        echo '</br>'.$z.' - '.$array_chat_id[$key];
                        if ($answer['ok'] AND $answer['result']['message_id']) {
                            echo ' - отправленно';
                            mysqli_query($CONNECT, "UPDATE `module_mailling` SET `count_ok` = `count_ok` + '1' WHERE `id` = '$mailling[id]'");
                        } else {                        
                            if ($answer['error_code'] == 403 OR ($answer['error_code'] == 400 AND $answer['description'] == 'Bad Request: chat not found')) {
                                echo ' - бот в бане-------------------------';
                                mysqli_query($CONNECT, "DELETE FROM `bot_user` WHERE `chat_id` = '$array_chat_id[$key]'");
                                mysqli_query($CONNECT, "UPDATE `module_mailling` SET `count_block` = `count_block` + '1' WHERE `id` = '$mailling[id]'");
                            } else {
                                mysqli_query($CONNECT, "UPDATE `module_mailling` SET `count_error` = `count_error` + '1' WHERE `id` = '$mailling[id]'");
                                if (!$answer['error_code']) {// если начал присылать null, то возможно ранее слал 429 что ниже
                                    $stop = true;
                                    echo ' - начал слать null';
                                    echo '</br>------------------</br><pre>';
                                    print_r($answer);
                                    echo '</pre>';    
                                    $retry_after = 30;   
                                } else if ($answer['error_code'] == 429) {// если слишком быстро, он пришлет сколько секунд подождать
                                    $stop = true;
                                    echo ' - поймали ошибку слишком много сообщений, данный заход остановлен';
                                    echo '</br>------------------</br><pre>';
                                    print_r($answer);
                                    echo '</pre>';                                     
                                    if ($answer['parameters']['retry_after'] AND $retry_after < $answer['parameters']['retry_after']) {$retry_after = $answer['parameters']['retry_after'];}
                                } else {
                                    $stop = true;
                                    echo ' - поймали ошибку неизвестную';
                                    echo '</br>------------------ $answer</br><pre>';
                                    print_r($answer);
                                    echo '</pre>';
                                }
                                $error_massive[$answer['error_code']][] = [
                                    'option' => $option,
                                    'answer' => $answer
                                ];                                
                            } 
                        }
                    } 
                } else {
                    echo '</br>Закончено</br>';
                    $mailling['date_finish'] = date('Y-m-d H:i:s', strtotime('now')); 
                    mysqli_query($CONNECT, "UPDATE `module_mailling` SET `status` = 'finish', `date_finish` = '$mailling[date_finish]' WHERE `id` = '$mailling[id]'");
                    echo '</br>------------</br>Завершена рассылка № '.$mailling['id'];
                    //рассылка админу который запустил
                    $mes_text .= "✅ Рассылка  № ".$mailling['id']." успешно завершена";
                    $mes_text .= "\n\nВсего пользователей: ".$mailling['count_all'];
                    $mes_text .= "\nОтправлено успешно: ".$mailling['count_ok'];
                    $mes_text .= "\nЗаблокировали бот с предыдущей рассылки: ".$mailling['count_block'];
                    $mes_text .= "\n\nЗапуск рассылки: ".preg_replace("/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/", '$1-$2-$3 $4:$5', $mailling['date_start']);
                    $mes_text .= "\nОкончание рассылки: ".preg_replace("/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/", '$1-$2-$3 $4:$5', $mailling['date_finish']);                    
                    $mes_text .= "\n\nЗатрачено времени: ".mailing_timer($mailling['date_start'], $mailling['date_finish']);
                    $sec = mailing_timer($mailling['date_start'], $mailling['date_finish'], 1);
                    if ($sec) {$count_sec = round($mailling['count_all'] / $sec, 1);}  else {$count_sec = 'не известно';}   
                    $mes_text .= "\nСообщений в секунду: ".$count_sec;
                    $option = ["chat_id" => $mailling['admin_chat_id'], "text" => $mes_text];
                    telegram("sendMessage", $option);
                }
            } else {
                $stop = true;
            }
            if ($retry_after > 0) {sleep($retry_after);}
            if ($stop) {break;}
        //}
        usleep(560000); //1000000 одна секунда
        if ($stop) {break;}
    }
    if ($error_massive) {file_put_contents(__DIR__.'/error_'.strtotime('now').'_answer.txt', json_encode($error_massive, true));}
    
    $now = date('Y-m-d H:i:s', strtotime('now')); 
    echo '</br></br></br>Финиш '.$now; 
    $finish = strtotime('now'); 
    $razn = $finish - $start;   
    echo '</br>Заняло секунд '.$razn; 
    if ($razn) {echo '</br>Сообщений в секунду '.($user_count / $razn);}     
    echo '</br>Покажет в боте: </br> в секунду '.($user_count / 60);
    echo '</br> в минуту '.($user_count );
    echo '</br> в час '.($user_count * 60);

} else {
    echo '</br></br></br>Нет подключения к bot_function.php'; 
}


?>