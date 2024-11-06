<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);    

if (in_array($chat_id, $worker) OR in_array($chat_id, $admin_chat_id)) { // админ и работники
    if ($result["message"]) {
        if (($result_photo OR $result_video) AND in_array($us['page'], ['number-add-file'])) {//загрузили фото к фильму	  
            if ($result_photo) {$file_type = 'photo';}  
            else if ($result_video) {$file_type = 'video';} 
            else  {$file_type = ''; $result_file_id = '';} 
            $control['t_2'] = text(text($control['t_2'], 'bot'));//странная хрень, но без нее не работает перенос из базы в базу
            $film_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `num` = '$control[t_1]'")); 
            if (!$film_check) {
                mysqli_query($CONNECT, "INSERT INTO `module_number_films` (`num`, `name`, `add_chat_id`, `file_type`, `file_id`) VALUES ('$control[t_1]', '$control[t_2]', '$us[chat_id]', '$file_type', '$result_file_id')");
            } else {
                mysqli_query($CONNECT, "UPDATE `module_number_films` SET `name` = '$control[t_2]', `add_chat_id` = '$us[chat_id]', `file_type` = '$file_type', `file_id` = '$result_file_id' WHERE `num` = '$control[t_1]'");
            }
            $go = 'number-add-save';

        } else if (isset($text) AND in_array($us['page'], ['number-list'])) {//список фильмов, удаляем фильм из списка
            if (preg_match("/^\/delete_[1-9][0-9]*$/", $text)) {
                $text = str_replace('/delete_', '', $text); 
                $check_film = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `num` = '$text'")); 
                if (in_array($chat_id, $admin_chat_id) OR $check_film['add_chat_id'] == $chat_id) {// админ может удалить любой фильм, работник только свой
                    mysqli_query($CONNECT, "DELETE FROM `module_number_films` WHERE `num` = '$text'");
                }
            }
            $go = $us['page'];
        } else if (isset($text) AND in_array($us['page'], ['number-add-num'])) {//ввод номера фильма 
            if (preg_match("/^[1-9][0-9]*$/", $text)) {
                mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_1` = '$text' WHERE `user_id` = '$us[id]'");
                $kino = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `num` = '$text'"));
                if ($kino) {
                    if ($chat_id == $kino['add_chat_id']) {
                        $go = 'number-add-rewrite';
                    } else if (in_array($chat_id, $admin_chat_id)) {
                        $go = 'number-add-rewrite';
                    } else {
                        $action[] = 'number-error-6';
                    }
                } else {
                    $go = 'number-add-name';
                }	
            } else {
                $action[] = 'number-error-4';
            }
        } else if (isset($text) AND in_array($us['page'], ['number-add-name'])) {//ввод имя фильма
            $new_text = text($text);
            mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_2` = '$new_text' WHERE `user_id` = '$us[id]'");
            $go = 'number-add-file';
        } else {
            $go = $us['page'];
        }
    } else if ($result["callback_query"]) {        
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        $param_2 = explode('_', $button)[3]; 
        if ($go == 'number-add-save') {
            $control['t_2'] = text(text($control['t_2'], 'bot'));//странная хрень, но без нее не работает перенос из базы в базу
            $film_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `num` = '$control[t_1]'")); 
            if (!$film_check) {
                mysqli_query($CONNECT, "INSERT INTO `module_number_films` (`num`, `name`, `add_chat_id`) VALUES ('$control[t_1]', '$control[t_2]', '$us[chat_id]')");
            } else {
                mysqli_query($CONNECT, "UPDATE `module_number_films` SET `name` = '$control[t_2]', `add_chat_id` = '$us[chat_id]', `file_type` = '', `file_id` = '' WHERE `num` = '$control[t_1]'");
            }
        } else if ($go == 'number-kinocount' AND isset($param)) {
            mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$param' WHERE `name` = 'number_count_film_worker_hide'");					
            $setting['number_count_film_worker_hide'] = $param;
        }
    }
} else { // пользователи
    $no_delete_user = true;
    $no_delete_bot = true;
    $no_old = true;
    $no_all_delete = true;
    if ($result["message"]) {
        if (in_array($us['page'], ['number-go']) AND isset($text)) {//написали текст  - код фильма      
            if ($text == "🔎 ИСКАТЬ ФИЛЬМ" OR $text == "🔎 ИСКАТЬ ФИЛЬМ ПО НОМЕРУ") {
                //просто перешлет сообщение заново
                $go = 'number-go';
            } else {
                $channels_error =  getChatMember($chat_id);
                if ($channels_error) {// Если юзер вышел хотя бы с одного канала
                    $go = '2401';
                } else {
                    if (preg_match("/^[1-9][0-9]*$/", $text)) {
                        $kino = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `num` = '$text'")); 
                        if ($kino) {								
                            $action[] = 'number-film-name';// в bot_telegram_send.php прописано сохранение number-film-name строки - наззвание фильма
                            mysqli_query($CONNECT, "UPDATE `module_number_films` SET `date_request` = '$now', `find_count` = `find_count` + 1 WHERE `num` = '$text'");
                        } else {
                            $action[] = 'number-error-1';
                        }
                    } else {
                        $action[] = 'number-error-2';
                    }	
                    $action[] = 'number-go';
                }
            }					
        } else {// при вводе текста не во время
            $action[] = $us['page'];
        }   
    } else if ($result["callback_query"]) {
        $go = explode('_', $button)[1];
        $param = explode('_', $button)[2];
        $param_2 = explode('_', $button)[3];    
        if ($go == 'number-go' AND $param == 'start') {
            $channels_error =  getChatMember($chat_id);
            if ($channels_error) {// обязательная проверка при нажатии на кнопку ПОДПИСАЛСЯ -  Если вошел не во все каналы
                $go = 2401;
                $action = [2402];
            } else {				
                mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = '1', `subscription_start` = '1' WHERE `chat_id` = '$chat_id'");
            }
            mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`, `old`) VALUES ('$chat_id', '$message_id', 'user_message', '1')");	            
        }
    }
}
?>