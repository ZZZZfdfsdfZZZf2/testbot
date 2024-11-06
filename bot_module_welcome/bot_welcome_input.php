<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);
if (in_array($chat_id, $admin_chat_id)) { // админ и работники
    if ($result["message"]) {
        if (in_array($us['page'], ['welcome-channel-add'])) {// добавление канала 
            if ($result['message']['forward_date'] AND $result['message']['forward_from_chat']['type'] == 'channel') {
                $text = $result['message']['forward_from_chat']['id'];
                $ch_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_welcome_channel` WHERE `channel_id` = '$text'"));
                if (!$ch_check) {
                    if (load_info_channel('module_welcome_channel', $text)) {// Бот проверяет есть ли бот на канале, и если есть, считывает инфу из канала и записывает ее в БД таблицу bot_channels
                        $action[] = 'welcome-channel-save';
                    } else {
                        $action[] = 'welcome-error-2';
                    }
                    $go = 'welcome-channel';
                } else {
                    $action[] = 'welcome-error-3';
                }
            } else {
                $action[] = 'welcome-error-1';
            }
        } else if (in_array($us['page'], ['welcome-channel-timer'])) {
            if (preg_match('/^[0-9]{1,3}$/', $text) AND $text >= 0 AND $text <= 290) {
                $load = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `setting` WHERE `name` = 'welcome_timer'")); //ранее не была создана, создаем заново если нету
                if (!$load) {
                    mysqli_query($CONNECT, "INSERT INTO `setting` (`name`, `param`) VALUES ('welcome_timer', '$text')");
                } else {
                    mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$text' WHERE `id` = $load[id]");
                }
                $go = 'welcome-channel-timer';                
                $setting['welcome_timer'] = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `setting` WHERE `name` = 'welcome_timer'"))['param'];
            } else {
                $action[] = 'welcome-error-4';
            }
        } else {
            $action[] = 'welcome-channel';
        }
    } else if ($result["callback_query"]) {        
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        $param_2 = explode('_', $button)[3]; 
        if ($go == 'welcome-channel-delete' AND $param) {// модуль приветсвия - каналы - канал удален
            mysqli_query($CONNECT, "DELETE FROM `module_welcome_channel` WHERE `id` = '$param'");
            $action[] = 'welcome-channel-delete';			
            $go = 'welcome-channel';
        }
    }
}
?>