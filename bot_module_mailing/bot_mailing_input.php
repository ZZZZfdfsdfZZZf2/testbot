<?php
include_once 'bot_mailing_config.php';
$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

if ($result["message"]) {//все что связано с вводом текста по рассылке
    if ($us['page'] == 'mailing-new')  {//добавление наполнения (текст фото и прочее)
        if (mb_stripos($text, '<') !== false OR mb_stripos($text, '>') !== false) {
            $action[] = 'mailing-error-2';
        } else if (preg_match('/^@/', $text)) {
            $action[] = 'mailing-error-3';
        } else {
            if ($text) {$text = mailing_text($text);}
            if ($result['message']['reply_markup']['inline_keyboard']) {
                $bd_keyboard = mailing_text(json_encode($result['message']['reply_markup']['inline_keyboard'], true));
            }
            if ($result_entities) {
                $bd_entities = mailing_text(json_encode($result_entities, true));
            }
            $go = 'mailing-create';
            mysqli_query($CONNECT, "INSERT INTO `module_mailling` (`status`, `admin_chat_id`, `text`, `file_type`, `file_id`, `keyboard`, `entities`, `preview`) VALUES ('create', '$chat_id', '$text', '$result_file_type', '$result_file_id', '$bd_keyboard', '$bd_entities', '1')");
            $mailling_id = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `module_mailling`"))[0];
        }
    } else if (mb_stripos($us['page'], 'mailing-button-text') !== false AND $text) {
        $mailling_id = explode('-', $us['page'])[3];
        $text = str_replace('"', '\"', $text); 
        $text = str_replace("'", "\'", $text); 
        $text = str_replace("\\", "\\\\", $text); 
        $bd_text = mailing_text($text);
        mysqli_query($CONNECT, "UPDATE `module_mailling` SET `button_dop` = '$bd_text' WHERE `id` = '$mailling_id'");
        $go = "mailing-button-link-".$mailling_id;
        $param = $mailling_id;  
    } else if (mb_stripos($us['page'], 'mailing-button-link') !== false AND $text) {
        $mailling_id = explode('-', $us['page'])[3];
        if (preg_match("/^(http|https|ftp):\/\/(www\.)?(\w|\d|-|\.|_|\/|&amp;|\?|\+|%|#|=)*$/i", $text)) {
            $gm = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `id` = '$mailling_id'"));
            if ($gm['keyboard']) {
                $mailing_buttons = json_decode(mailing_text($gm['keyboard'], 'bot'), true);
            }
            $mailing_button_text = mailing_text($gm['button_dop'], 'bot');
            $mailing_buttons[] = [["text" => $mailing_button_text, "url" => $text]];
            $bd_button = mailing_text(json_encode($mailing_buttons, true));
            mysqli_query($CONNECT, "UPDATE `module_mailling` SET `keyboard` = '$bd_button' WHERE `id` = '$mailling_id'");
            $go = "mailing-create";
        } else {
            $action[] = 'mailing-error-1';
        }
        $param = $mailling_id;
    } else if (mb_stripos($us['page'], 'mailing-time-edit') !== false  AND ($text OR $text === '0')) {
        $mailling_id = explode('-', $us['page'])[3];
        if (preg_match("/^[0-9]+$/", $text)) {
            if ($text > 0 AND $text <= 50000) {
                $date_wait = date('Y-m-d H:i', strtotime('+'.$text.' minutes')).":00"; 
            } else {
                $action[] = 'mailing-error-4';
            }
        } else if (preg_match("/^20[2-3][0-9]-[0-1][0-9]-(3[0-1]|[0-2][0-9])\s(2[0-3]|[0-1][0-9]):[0-5][0-9]$/", $text)){//2022-09-01 09:30       
            if (strtotime($text.":00") > strtotime('now')) {
                $date_wait = $text.":00";
            } else {
                $action[] = 'mailing-error-6';
            }
        } else {
            $action[] = 'mailing-error-5';
        } 
        if ($date_wait) {
            mysqli_query($CONNECT, "UPDATE `module_mailling` SET `date_wait` = '$date_wait' WHERE `id` = '$mailling_id'");
            $go = "mailing-create";
        }
    } else {//при вводе текста не во время
        $action[] = $us['page'];
    }    
} else if ($result["callback_query"]) {//все что связано с кнопками по рассылке
    $go = explode('_', $button)[1];
    $param = explode('_', $button)[2];
    $param_2 = explode('_', $button)[3];
    if ($go == 'mailing-send' AND $param) {
        mysqli_query($CONNECT, "UPDATE `module_mailling` SET `status` = 'start' WHERE `id` = '$param'");
    } else if ($go == 'mailing-create' AND $param) {
        $mailling_id = $param;
    } else if ($go == 'mailing-cancel') {
        if ($param) {						
            $mailing_load = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `id` = '$param'")); 
            telegram("deleteMessage", ["chat_id" => $mailing_load['admin_chat_id'], "message_id" => $mailing_load['message_id']]);
            mysqli_query($CONNECT, "DELETE FROM `module_mailling` WHERE `id` = '$param'");
        }					
        $go = $param_2;
    } else if ($go == 'mailing-buttonclean' AND $param) {
        mysqli_query($CONNECT, "UPDATE `module_mailling` SET `keyboard` = '' WHERE `id` = '$param'");
        $go = 'mailing-create';
        $mailling_id = $param;
        $mailing_button_clean = true;
    } else if ($go == 'mailing-preview' AND $param) {
        mysqli_query($CONNECT, "UPDATE `module_mailling` SET `preview` = '$param_2' WHERE `id` = '$param'");
        $go = 'mailing-create';        
        $mailling_id = $param;
        $switch_preview = true;
    } else if ($go == 'mailing-save' AND $param) {
        $mailling_option = ["chat_id" => $chat_id, "text" => "⬆️ Рассылка №".$param." ⬆️"];
        telegram("sendMessage", $mailling_option);
        $go = $param_2;
        $no_old = true;
    } else if ($go == 'mailing-delete' AND $param) {
        mysqli_query($CONNECT, "DELETE FROM `module_mailling` WHERE `id` = '$param'");
        $action[] = 'mailing-delete';
        $go = 'mailing-list';
    }	
}
?>