<?php
include_once 'bot_filmlist_config.php';

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

if (in_array($chat_id, $worker) OR in_array($chat_id, $admin_chat_id)) { // админ и работники
    if ($result["message"]) {
        if (in_array($us['page'], ['filmlist-look-add']) AND $text) {
            $text = text($text);
            mysqli_query($CONNECT, "INSERT INTO `module_filmlist_look` (`name`) VALUES ('$text')");
            $action[] = "filmlist-look-add-ok";
            $go = 'filmlist-look-list';
        }
    } else if ($result["callback_query"]) { 
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        if ($go == 'filmlist-look-delete') {
            mysqli_query($CONNECT, "DELETE FROM `module_filmlist_look` WHERE `id` = '$param'");
            $go = 'filmlist-look-list';
        }        
    }
} else { // пользователи
    $no_delete_user = true;
    $no_delete_bot = true;
    $no_old = true;
    $no_all_delete = true;
    if ($result["message"]) {
        if ($text == $menu_back AND $module['menu']) {// если есть модуль  меню menu эта кнопка перекинет в меню
            $go = 'menu';
        } else {// при вводе текста не во время
            $action[] = 'menu';
        } 
        $no_old = true; 
    } else if ($result["callback_query"]) {
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        $param_2 = explode('_', $button)[3]; 
        $param_3 = explode('_', $button)[4];    
        if ($go == 'filmlist-go') {
            $channels_error = getChatMember($chat_id);
            if ($channels_error) {// обязательная проверка при нажатии на кнопку ПОДПИСАЛСЯ -  Если вошел не во все каналы
                $action = [2402, 2401];
            } else if ($param == 'start') {						
                mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = '1', `subscription_start` = '1' WHERE `chat_id` = '$chat_id'");
            }
            mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`, `old`) VALUES ('$chat_id', '$message_id', 'user_message', '1')");
        } else if ($go == 'filmlist-genre' OR $go == 'filmlist-top-list') {
            if ($param) {
                mysqli_query($CONNECT, "UPDATE `bot_user` SET `filmlist_1` = '$param', `filmlist_2` = '$param_2', `filmlist_3` = '$param_3' WHERE `id` = $us[id]");
            }
        } else if (in_array($go, ['filmlist-look-message', 'filmlist-random', 'filmlist-show', 'filmlist-top'])) {
            $channels_error = getChatMember($chat_id);
            if ($channels_error) {// обязательная проверка при нажатии на кнопку ПОДПИСАЛСЯ -  Если вошел не во все каналы
                $action = [2402, 2401];
                $go = '';
            }
        }
    }
}
?>