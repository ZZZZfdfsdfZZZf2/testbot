<?php
//обновить  названия каналаов и их @name если они изменились
$check_channel = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_welcome_channel` WHERE `channel_id` = '$channel_id'"));
if ($check_channel) {
    $new_title = text($channel_title);
    if ($check_channel['title'] != $new_title) {mysqli_query($CONNECT, "UPDATE `module_welcome_channel` SET `title` = '$new_title' WHERE `id` = '$check_channel[id]'");}
    if ($channel_username AND mb_stripos($channel_username, '@') === false) {$channel_username = "@".$channel_username;}
    if ($check_channel['channel_name'] != $channel_username) {mysqli_query($CONNECT, "UPDATE `module_welcome_channel` SET `channel_name` = '$channel_username' WHERE `id` = '$check_channel[id]'");}
}
if ($result["chat_join_request"]) {   

    // если пользвоатель запустил по специальной сслке бота, то пустить его в бот, и записать его в БД 
    $check_wc = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_welcome_channel` WHERE `channel_id` = '$channel_id'")); 
    if ($check_wc) {        
        // ============ даем вход на канал
        // ============ даем вход на канал
        $option = ["chat_id" => $channel_id, "user_id" => $chat_id];
        $a1 = telegram("approveChatJoinRequest", $option);
        $a2 = ['option' => $option, 'answer' => $a1];
        // добавляем юзера
        $us = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `chat_id` = '$chat_id'")); 
        if (!$us) {
            mysqli_query($CONNECT, "INSERT INTO `bot_user` (`chat_id`, `username`) VALUES ('$chat_id', '$username')");
            //если есть full_name колонка, дописываем
            $coloumn_string_w = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'bot_user'"))[0];
            $coloumn_array_w = explode(',', $coloumn_string_w);
            if (in_array('full_name', $coloumn_array_w)) {
                $full_name = text($first_name." ".$last_name);
                mysqli_query($CONNECT, "UPDATE `bot_user` SET `full_name` = '$full_name' WHERE `chat_id` = '$chat_id'");
            }
            $us = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `chat_id` = '$chat_id'"));
        }
        //если не было юзера раньше в боте отправялем сообщение
        $check_user_subscription = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_subscription` WHERE `chat_id` = '$chat_id'")); 
        if (!$check_user_subscription) {
            mysqli_query($CONNECT, "UPDATE `module_welcome_channel` SET `count` = `count` + '1' WHERE `id` = '$check_wc[id]'");
            $go = 'user-start';
			//if ($setting['start_message_hide']) {$go = $user_module_connect_page;} else {$go = 'user-start';}
            // делаем задержку времени у сообщения если нужно
            if ($setting['welcome_timer'] AND $setting['welcome_timer'] > 0) {$arr_plus['user-start']["timer"] = "+".$setting['welcome_timer']." seconds";}
            mysqli_query($CONNECT, "INSERT INTO `bot_user_subscription` (`chat_id`, `date_write`) VALUES ('$chat_id', '$now_date')");
            $action[] = $go;
            $no_signal = true;
            mysqli_query($CONNECT, "UPDATE `bot_user` SET `page` = '$go' WHERE `id` = $us[id]");
        }
    }
}
?>