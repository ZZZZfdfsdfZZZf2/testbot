<?php

include 'bot_dialog_menu.php';

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

if (in_array($chat_id, $admin_chat_id)) { // админ 
    if ($result["message"]) {
        if (in_array($us['page'], ['refer-add-worker'])) {// добавление работника
            if (($text OR $text === '0') AND preg_match("/^@([0-9]|[a-z]|_){5,}$/i", $text)) {
                $text = str_replace('@', '', $text);
                $customer = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `username` = '$text'"));
                if (!$customer) {
                    $action[] = 'refer-error-4';
                } else if (in_array($customer['chat_id'], $admin_chat_id)) {
                    $action[] = 'refer-error-3';
                } else if (in_array($customer['chat_id'], $worker)) {
                    $action[] = 'refer-error-2';
                } else {
                    $worker[] = $customer['chat_id'];
                    $action[] = 'refer-add-worker-ok';
                    $go = 'refer-list';
                    //отправляем сообщение работнику
                    $worker_message = "‼️ Вам открыли доступ в боте к реферальной системе и добавлению фильмов, нажмите кнопку ниже чтобы перейти к меню";
                    $worker_keyboard = [[["text" => "ПЕРЕЙТИ К МЕНЮ ➡️", "callback_data" => "refer_refer-worker-menu_start"]]];								
                    $worker_option = ["chat_id" => $customer['chat_id'], "text" => $worker_message, "keyboard" => $worker_keyboard];
                    $ans = telegram("sendMessage", $worker_option);
                    if ($ans['result']) {
                        $fn = text($ans['result']['chat']['first_name']);
                        $ln = text($ans['result']['chat']['last_name']);
                    }  
                    mysqli_query($CONNECT, "INSERT INTO `bot_user_admin` (`user_id`, `chat_id`, `status`, `first_name`, `last_name`, `username`) VALUES ('$customer[id]', '$customer[chat_id]', 'worker', '$fn', '$ln', '$text')");
                }
            } else {
                $action[] = 'refer-error-1';							
            }						 
        } else if (($text OR $text === '0') AND in_array($us['page'], ['refer-set-proc-wait'])) {// setting - Скрытый процент
            if (preg_match("/^([0-9]|[1-9]?[0-9]|100)$/", $text)) {
                mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$text' WHERE `name` = 'hide_percent'");
                $setting['hide_percent'] = $text;
                $go = 'refer-set-proc';
            } else {
                $action[] = 'error-number-4';
            }
        } else {
            $go = $us['page'];
        }
    } else if ($result["callback_query"]) {        
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        $param_2 = explode('_', $button)[3]; 
        if ($go == 'refer-delete-worker' AND $param) {// удалить работника - переведет его в обычного
            //зачищаем у всех кто перешел по его рефке, что они перешли по его рефке
            $load = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `id` = '$param'"));
            if ($load['chat_id']) {
                mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `refer_id` = '' WHERE `refer_id` = '$load[chat_id]'");
            } 
            // удаляем юзера из работников
            mysqli_query($CONNECT, "DELETE FROM `bot_user_admin` WHERE `id` = '$param'");
            $action[] = 'refer-delete-worker';
            $go = 'refer-list';
            // подгружаем заново работников
            $worker = array();
            $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `status` = 'worker'");
            while($row = mysqli_fetch_assoc($sql)) {
                $worker[] = $row['chat_id'];	
            }
        } else if ($go == 'refer-delete-refer' AND $param) {
            $load = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `id` = '$param'"));
            if ($load['chat_id']) {
                mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `refer_id` = '' WHERE `refer_id` = '$load[chat_id]'");
            }
            if ($module['money']) {
                mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `refer_pay` = '0' WHERE `id` = '$param'");
            }
            $action[] = 'refer-delete-refer';
            $go = 'refer-worker';
        } else if ($go == 'refer-set-tariff' AND $param) {// setting - изменение вывода счетчика ля рефералов	
            mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$param' WHERE `name` = 'referal_count'");					
            $setting['referal_count'] = $param;
        } else if ($go == 'refer-set-stat' AND $param) {// setting - скрыть показать реф статистику пользователю
            mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$param' WHERE `name` = 'worker_reff_stat_show'");					
            $setting['worker_reff_stat_show'] = $param;
        }
    }
} else if (in_array($chat_id, $worker)) {// ====================== работники ======================
    if ($result["message"]){
        if ($text == '/start'  OR preg_match('/^\/start\s/', $text) /* mb_stripos($text, '/start ') !== false */) {
            $go = 'refer-worker-menu';
        } else if (preg_match('/^money/', $us['page']) AND $module['money']) {// подключаемый модуль рассылки money 
            include_once $module['money'];
        } else if (preg_match('/^number/', $us['page']) AND $module['number']) {// подключаемый модуль фильмы_по_номеру number
            include_once $module['number']['input'];
        } else if (preg_match('/^filmshow/', $us['page']) AND $module['filmshow']) {// подключаемый модуль просмотра фильмов в телеге
            include_once $module['filmshow']['input'];
        } else {
            if ($text AND in_array($us['page'], [])) {// 
            } else {
                $action[] = 'refer-worker-menu';
            }				
        }
    } else if ($result["callback_query"]){			
        if (preg_match('/^action_/', $button)) {           
            $go = explode('_', $button)[1];
            $param = explode('_', $button)[2];
        } else if (preg_match('/^money_/', $button) AND $module['money']) { // подключаемый модуль рассылки money 
            include_once $module['money']['input'];
        } else if (preg_match('/^number_/', $button) AND $module['number']) { // подключаемый модуль фильмы_по_номеру number
            include_once $module['number']['input'];
        } else if (preg_match('/^refer_/', $button)) {// подключаемый модуль filmlist           
            $go = explode('_', $button)[1];
            $param = explode('_', $button)[2];
            if ($go == 'refer-worker-menu' AND $param == 'start') {
                telegram("deleteMessage", ["chat_id" => $chat_id, "message_id" => $message_id]);
            } 
        } else if (preg_match('/^filmshow_/', $button) AND $module['filmshow']) { // подключаемый модуль фильмы_по_номеру number
            include_once $module['filmshow']['input'];
        } else if ( $button == 'close') {// удалить сообщение нажав на кнопку
            telegram("deleteMessage", ["chat_id" => $chat_id, "message_id" => $message_id]);
        }
    }
} else { // пользователи    
    if ($result["message"]) {
        if (preg_match('/^refer/', $us['page'])) {// подключаемый модуль фильмы_по_номеру number
            $action[] = 2401; 
        }
    } else if ($result["callback_query"]) { 
        if (preg_match('/^refer_/', $button)) {// подключаемый модуль filmlist   
            $action[] = 2401;          
        }
    }
}
?>