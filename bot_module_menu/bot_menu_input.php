<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

if (in_array($chat_id, $worker) OR in_array($chat_id, $admin_chat_id)) { // админ и работники
    if ($result["message"]) {
    } else if ($result["callback_query"]) {    
    }
} else { // пользователи
    $no_delete_user = true;
    $no_delete_bot = true;
    $no_all_delete = true;
    $no_old = true;
    //$no_all_delete = true;
    if ($result["message"]) {
        $action[] = $us['page'];        
        $no_old = true;   
    } else if ($result["callback_query"]) {
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        $param_2 = explode('_', $button)[3];    
        if ($go == 'menu') {
            if ($param == 'start') {
                $channels_error =  getChatMember($chat_id);
                if ($channels_error) {// обязательная проверка при нажатии на кнопку ПОДПИСАЛСЯ -  Если вошел не во все каналы
                    unset($go);
                    $go = 2401;
                    $action = [2402];
                    $no_delete_bot = false;
                    $no_old = false;
                    $no_all_delete = false;
                } else {
                    $no_old = true;
                    $no_delete_bot = true;
                    $no_all_delete = false;					
                    mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = '1', `subscription_start` = '1' WHERE `chat_id` = '$chat_id'");
                }
            } 
            mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`, `old`) VALUES ('$chat_id', '$message_id', 'user_message', '1')");
        }
    }
}
?>