<?php
include_once 'bot_mailing_config.php';

if (!$dop_but['back']) { $dop_but['back'] = "⬅️ Назад";}

if (in_array('mailing-new', $action)) {
    $no_old = true;
    $arr['mailing-new']["text"] = "💬 Отправьте сообщения для рассылки:";
    $arr['mailing-new']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];
}

if (mb_stripos($action[0], 'mailing-button-text') !== false) {
    $arr['mailing-button-text']["text"] = "➕ Добавление кнопки.\n\nНапишите <b>текст</b> на кнопке ";
    $arr['mailing-button-text']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];
    $action[0] = 'mailing-button-text';
}

if (mb_stripos($action[0], 'mailing-button-link') !== false) {
    $arr['mailing-button-link']["text"] = "➕ Добавление кнопки.\n\n✅ Текст на кнопке: ''".$text."''\n\nНапишите <b>ссылку</b> на кнопке ";
    $arr['mailing-button-link']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];
    $action[0] = 'mailing-button-link';
}

if (in_array('mailing-list', $action)) {// Отчет по рассылкам
    $no_old = true;
    $arr['mailing-list']["text"] .= "📣 Отчет по рассылкам:";
    //в очереди
    $count1 = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_mailling` WHERE `status` = 'start'"))[0];
    if ($count1) {
        $arr['mailing-list']["text"] .= "\n\n✉️ В очереди:";
        $sql = mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `status` = 'start' ORDER BY `id` DESC");
        while($row = mysqli_fetch_assoc($sql) ){
            $arr['mailing-list']["text"] .= "\n<pre>Рассылка № ".$row['id'];
            if ($row['date_wait'] != '0000-00-00 00:00:00') {$arr['mailing-list']["text"] .= " - ⏱ Заупуск ".preg_replace("/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/", '$1-$2-$3 $4:$5', $row['date_wait']);;}
            $arr['mailing-list']["text"] .= "</pre>";
        }
    }
    //активная
    $stop = array();
    $count2 = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_mailling` WHERE `status` = 'load_user' OR `status` = 'progress'"))[0];
    if ($count2) {
        $arr['mailing-list']["text"] .= "\n\n📩 Активная:";
        $sql = mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `status` = 'load_user' OR  `status` = 'progress' ORDER BY `id` DESC");
        while($row = mysqli_fetch_assoc($sql) ){
            $arr['mailing-list']["text"] .= "\n<pre>Рассылка № ".$row['id'];
            if ($row['count_ok'] AND $row['count_all']) {
                $mailing_proc = round((100 * ($row['count_ok'] + $row['count_block'])) / $row['count_all'], 1);
            } else {
                $mailing_proc = 0;
            }
            $arr['mailing-list']["text"] .= "\nПредположительно будет разослано: ".$row['count_all'];
            $arr['mailing-list']["text"] .= "\nОтправлено успешно: ".$row['count_ok'];
            $arr['mailing-list']["text"] .= "\nЗаблокированных: ".($row['count_block'] + $row['count_error']);
            if ($chat_id == 355590439 AND $row['count_error']) {$arr['mailing-list']["text"] .= "\n‼️ Ошибок: ".$row['count_error'];}
            $arr['mailing-list']["text"] .= "\n";
            $proc_dec = round($mailing_proc / 10);
            for ($i = 1; $i <= 10; $i++) {
                if ($proc_dec >= $i) {
                    $arr['mailing-list']["text"] .= "🟩";
                } else {
                    $arr['mailing-list']["text"] .= "⬛️";
                }                
            }
            $arr['mailing-list']["text"] .= " ".$mailing_proc."%</pre>";
        }
    } 
    //последняя законченная
    $count3 = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_mailling` WHERE `status` = 'finish'"))[0];
    if ($count3) {
        $sql = mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `status` = 'finish' ORDER BY `id` DESC LIMIT 1");
        while($row = mysqli_fetch_assoc($sql) ){
            $sec = mailing_timer($row['date_start'], $row['date_finish'], 1);
            if ($sec) {$count_sec = round($row['count_all'] / $sec, 1);}  else {$count_sec = 'не известно';}         
            $arr['mailing-list']["text"] .= "\n\n📨 Последняя законченная \n<pre>";
            $arr['mailing-list']["text"] .= "Рассылка № ".$row['id'];
            $arr['mailing-list']["text"] .= "\nОтправлено успешно: ".$row['count_ok'];
            $arr['mailing-list']["text"] .= "\nЗаблокированных: ".($row['count_block'] + $row['count_error']);
            if ($chat_id == 355590439 AND $row['count_error']) {$arr['mailing-list']["text"] .= "\n‼️ Ошибок: ".$row['count_error']."\n";}
            $arr['mailing-list']["text"] .= "\nЗапуск рассылки: ".$row['date_start'];
            $arr['mailing-list']["text"] .= "\nОкончание рассылки: ".$row['date_finish'];
            $arr['mailing-list']["text"] .= "\nЗатрачено времени: ".mailing_timer($row['date_start'], $row['date_finish']);
            $arr['mailing-list']["text"] .= "\nСообщений в секунду: ".$count_sec;
            $arr['mailing-list']["text"] .= "</pre>";
        }
    }
    if (!$count1 AND !$count2 AND !$count3) {
        $arr['mailing-list']["text"] .= "\n\n✖️ У вас не создано ни одной рассылки";
    }
    $sql = mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `status` = 'load_user' OR  `status` = 'progress' OR `status` = 'start' ORDER BY `id` ASC");
    while($row = mysqli_fetch_assoc($sql) ){
        $arr['mailing-list']["keyboard"][] = [["text" => "❌ Сбросить рассылку № ".$row['id'], "callback_data" => "mailing_mailing-delete_".$row['id']]];
    }
    $arr['mailing-list']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];
}

if (in_array('mailing-create', $action)) {// прислали  сообщение для рассылки
    $gm = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `id` = '$mailling_id'"));
    if ((!$param OR $switch_preview) AND !$date_wait) {// Отправили сообщение, а не вернулись с кнопок
        $mailing_option['chat_id'] = $chat_id;
        if ($gm['text']) {$gm['text'] = mailing_text($gm['text'], 'bot');}
        if ($gm['keyboard']) {$mailing_option['keyboard'] = json_decode(mailing_text($gm['keyboard'], 'bot'), true);}
        if ($gm['entities']) {$mailing_option['entities'] = json_decode(mailing_text($gm['entities'], 'bot'), true);}

        if ($gm['file_id'] AND $gm['file_type']){
            $mailing_option[$gm['file_type']] = $gm['file_id'];
            if ($gm['file_type'] == 'video_note') {
                $types_send = 'sendVideoNote';
            } else {
                $mailing_option['caption'] = $gm['text'];
                $types_send = "send".ucfirst($gm['file_type']); // ucfirst - первая буква заглавная
            }            
            $mailing_answer = telegram($types_send, $mailing_option);
        } else {
            $mailing_option['preview'] = $gm['preview'];
            $mailing_option['text'] = $gm['text'];
            $mailing_answer = telegram("sendMessage", $mailing_option);
        }
        $mailing_answer_id = $mailing_answer['result']['message_id'];
        if ($switch_preview) {telegram("deleteMessage", ["chat_id" => $gm['admin_chat_id'], "message_id" => $gm['message_id']]);}
        mysqli_query($CONNECT, "UPDATE `module_mailling` SET `message_id` = '$mailing_answer_id' WHERE `id` = '$mailling_id'");    
        file_put_contents(__DIR__.'/mailing_option.txt', json_encode($mailing_option, true));
        file_put_contents(__DIR__.'/mailing_answer.txt', json_encode($mailing_answer, true));
    }
    if ($mailing_buttons OR $mailing_button_clean) {
        $mailing_option = ["chat_id" => $gm['admin_chat_id'], "message_id" => $gm['message_id'], "keyboard" => $mailing_buttons];
        $mailing_answer = telegram("editMessageReplyMarkup", $mailing_option);  
        file_put_contents(__DIR__.'/mailing_option.txt', json_encode($mailing_option, true));
        file_put_contents(__DIR__.'/mailing_answer.txt', json_encode($mailing_answer, true));
    }
    $arr['mailing-create']["text"] = "⬆️ Проверьте сообщение выше ⬆️\n        и подтвердите отправку ";
    $arr['mailing-create']["keyboard"][] = [["text" => "➕ Добавить кнопку", "callback_data" => "mailing_mailing-button-text-".$mailling_id."_".$mailling_id]];
    if ($gm['keyboard']) {$arr['mailing-create']["keyboard"][] = [["text" => "✖️ Удалить все кнопки", "callback_data" => "mailing_mailing-buttonclean_".$mailling_id]];}  
    if ($gm['entities'] AND !$gm['file_id']) {// проверяем есть ли сслки которые откробются как преью у сообщения
        $gm_entities = json_decode(mailing_text($gm['entities'], 'bot'), true);
        foreach($gm_entities as $value) {
            if ($value['type'] == 'url' OR $value['type'] == 'text_link') {$show_link = true; break;}
        }
    } 
    if ($show_link) {
        if ($gm['preview']) {
            $arr['mailing-create']["keyboard"][] = [["text" => "Превью - будет показано 👁‍🗨", "callback_data" => "mailing_mailing-preview_".$mailling_id."_0"]];
        } else {
            $arr['mailing-create']["keyboard"][] = [["text" => "Превью - будет скрыто 🙈", "callback_data" => "mailing_mailing-preview_".$mailling_id."_1"]];
        }
    }
    if ($gm['date_wait'] == '0000-00-00 00:00:00') {$gm_timer = 'сразу';} else {$gm_timer = $gm['date_wait'];}
    $arr['mailing-create']["keyboard"][] = [["text" => "⏱ Запуск: ".$gm_timer, "callback_data" => "mailing_mailing-time-edit-".$mailling_id]]; 
    $arr['mailing-create']["keyboard"][] = [["text" => "✅ Отправить", "callback_data" => "mailing_mailing-send_".$mailling_id], ["text" => "🚫 Отмена", "callback_data" => "mailing_mailing-cancel_".$mailling_id."_".explode('_', $b_back)[1]]];
    $no_old = true;
}

$arr['mailing-delete']["text"] = "✔️ Рассылка № ".$param." удалена";


if (mb_stripos($action[0], 'mailing-time-edit') !== false) {
    $arr['mailing-time-edit']["text"] = "⏱ Запуск рассылки через определенный период времени\n\nНапишите цифрой через сколько минут начать, либо полную дату и время в формате: 2022-09-21 20:30\n\n❕ Указывать московское время.\n\n❕ Если в назначенное время будет уже идти другая рассылка, то данная рассылка, запуститься только после завершения запущенной рассылки";
    $arr['mailing-time-edit']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];
    $action[0] = 'mailing-time-edit';
}




$arr['mailing-send']["text"] = "✅ Рассылка №".$param." сохранена и отправлена в очередь на рассылку пользователям в бот";
$arr['mailing-send']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-save_".$param."_".explode('_', $b_back)[1]]];

$arr['mailing-error-1']["text"] = "❌ Ссылка не верна, попробуйте еще раз.\n\nСсылка на телеграм каналы или боты должан быть в формате https://t.me/name";
$arr['mailing-error-1']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];


$arr['mailing-error-2']["text"] = "❌ Не используйте треугольные скобки в сообщении \n\n💬 Отправьте сообщения для рассылки:";
$arr['mailing-error-2']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];


$arr['mailing-error-3']["text"] = "❌ Первый символ в сообщении не может быть - @. Поставьте перед ним любой другой символ(не пробел) или смайлик   \n\n💬 Отправьте сообщения для рассылки:";
$arr['mailing-error-3']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];

$arr['mailing-error-4']["text"] = "❌ Не верное число.\n\n Если вы указываете через сколько минут запустить рассылку, то число должно быть больше нуля и меньше 50000";
$arr['mailing-error-4']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];

$arr['mailing-error-5']["text"] = "❌ Не верное дата и время.\n\n Напишите цифрой через сколько минут начать, либо полную дату и время в формате: <b>2022-09-21 20:30</b> , соблюдая все тире и двоеточие";
$arr['mailing-error-5']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];

$arr['mailing-error-6']["text"] = "❌ Дата должна быть в будущем, а не в прошедшем. \nСейчас: ".date('Y-m-d H:i', strtotime('now'))." по Москве";
$arr['mailing-error-6']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "mailing_mailing-create_".$param]];
?>