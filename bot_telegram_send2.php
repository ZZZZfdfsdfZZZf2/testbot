<?php

/* 
// добавить дважды в location @fallback {

proxy_connect_timeout 100;
proxy_send_timeout 100;
proxy_read_timeout 100;
send_timeout 100;
 */

if (count($action)){
	//$delete_old = array();
	if ($message_id AND !$no_delete_user AND $result["message"]) {
		mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`, `old`) VALUES ('$chat_id', '$message_id', 'user_message', '1')");
	}
	if (!$no_all_delete) {
		//создать список сообщений что удаляем
		mysqli_query($CONNECT, "UPDATE `bot_user_history` SET `old` = '1' WHERE `chat_id` = '$chat_id'");
		/* $history_sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_history` WHERE `chat_id` = '$chat_id'");
		while($history = mysqli_fetch_assoc($history_sql) ){
			$delete_old[] = $history['message_id'];	
		} */
	} 
	//отправляем сообщения
	foreach($action as $key => $value) {
		$write_bd = array();
		$answer_message_id = '';
		$bot_answer = '';
		$answer_edit = '';
		$old_mes = '';
		$write_bd['type'] = '';
		if ($arr[$value]['text'] OR $arr[$value]['photo'] OR $arr[$value]['media'] OR $arr[$value]['keyboard'] OR $arr[$value]['sticker'] OR $arr[$value]['alert'] OR $arr[$value]['question'] OR $arr[$value]['money'] OR $arr[$value]['video'] OR $arr[$value]['game_short_name'] OR $arr[$value]['latitude'] OR $arr[$value]['document'] OR $arr[$value]['act']){			
			//удалить память - начало

			if ($arr[$value]['text'] AND $us['full_name']) {
				if (mb_stripos($arr[$value]['text'], '%NAME%') !== false OR mb_stripos($arr[$value]['text'], '%FIRST_NAME%') !== false) {
					$arr[$value]['text'] = str_replace(['%NAME%', '%FIRST_NAME%'], text($us['full_name'], 'bot'), $arr[$value]['text']); 
				}
			}

			//пытаемся не послать новое сообщение а заменить старое, ищем подобное по типу в истории, и если находим то будем менять			
			//тип сообщения
			if ($arr[$value]['document']) {$write_bd['type'] = 'sendDocument';} 
			else if ($arr[$value]['sticker']) {$write_bd['type'] = 'sendSticker';}
			else if ($arr[$value]['emoji']) {$write_bd['type'] = 'sendDice';}
			else if ($arr[$value]['photo']) {$write_bd['type'] = 'sendPhoto';}
			else if ($arr[$value]['video']) {$write_bd['type'] = 'sendVideo';}
			else if ($arr[$value]['media']) {$write_bd['type'] = 'sendMediaGroup';}
			else if ($arr[$value]['text']) {$write_bd['type'] = 'sendMessage';}
			else if ($arr[$value]['question']) {$write_bd['type'] = 'sendPoll';}
			else if ($arr[$value]['game_short_name']) {$write_bd['type'] = 'sendGame';}
			else if ($arr[$value]['latitude']) {$write_bd['type'] = 'sendLocation';}
			else if ($arr[$value]['money']) {$write_bd['type'] = 'sendInvoice';}
			else if ($arr[$value]['alert']) {$write_bd['type'] = 'answerCallbackQuery';}
			else if ($arr[$value]['keyboard']) {$write_bd['type'] = 'editMessageReplyMarkup';}
			else if ($arr[$value]['act']) {$write_bd['type'] = 'sendChatAction';}
			
			if ($arr[$value]['photo'] AND mb_stripos($arr[$value]['photo'], 'http') === false AND mb_stripos($arr[$value]['photo'], '.') !== false) {// если фото, если нет начала ссылки, если это название файла а не file_id
				if (mb_stripos($arr[$value]['photo'], '/') === false) {$arr[$value]['photo'] = $website.$folder_bot_files.$arr[$value]['photo'];}//если image.jpg то берем из папки bot_files 
				else {$arr[$value]['photo'] = $website.'/'.$arr[$value]['photo'];}// если other/image.jpg
			} else if ($arr[$value]['video'] AND mb_stripos($arr[$value]['video'], 'http') === false AND mb_stripos($arr[$value]['video'], '.') !== false) {// если видео, если нет начала ссылки, если это название файла а не file_id
				if (mb_stripos($arr[$value]['video'], '/') === false) {$arr[$value]['video'] = $website.$folder_bot_files.$arr[$value]['video'];}//если image.jpg то берем из папки bot_files 
				else {$arr[$value]['video'] = $website.'/'.$arr[$value]['video'];}// если other/image.jpg
			} else if ($arr[$value]['document'] AND mb_stripos($arr[$value]['document'], 'http') === false AND mb_stripos($arr[$value]['document'], '.') !== false AND mb_stripos($arr[$value]['document'], '.txt') === false) {// если файл, если нет начала ссылки, если это название файла а не file_id
				if (mb_stripos($arr[$value]['document'], '/') === false) {$arr[$value]['document'] = $website.$folder_bot_files.$arr[$value]['document'];}//если image.jpg то берем из папки bot_files 
				else {$arr[$value]['document'] = $website.'/'.$arr[$value]['document'];}// если other/image.jpg
			} else if ($arr[$value]['media']) {
				foreach($arr[$value]['media'] as $key => $ar_med) {
					if (mb_stripos($ar_med, 'http') === false) {
						if (mb_stripos($ar_med, '/') === false) {$arr[$value]['media'][$key] = $website.$folder_bot_files.$ar_med;} // если image.jpg то берем из папки bot_files 
						else {$arr[$value]['media'][$key] = $website.'/'.$ar_med;} // если other/image.jpg
					}
				}
			}
			if ($arr[$value]['chat_id']) {$write_bd['chat_id'] = $arr[$value]['chat_id'];} else {$write_bd['chat_id'] = $chat_id;}
			if ($arr[$value]['keyboard']) {$write_bd['keyboard'] = text(json_encode($arr[$value]['keyboard'], true));}
			if ($arr[$value]['menu']) {$write_bd['menu'] = text(json_encode($arr[$value]['menu'], true));}
			if ($arr[$value]['entities']) {$write_bd['entities'] = json_encode($arr[$value]['entities'], true);}
			if ($arr[$value]['text']) {$write_bd['text'] = text($arr[$value]['text']);}
			if ($arr[$value]['alert']) {$write_bd['text'] = text($arr[$value]['alert']); $write_bd['edit_message_id'] = $global_id; $write_bd['keyboard'] = 1;}
			if ($arr[$value]['emoji']) {$write_bd['text'] = $arr[$value]['emoji'];}
			if ($arr[$value]['photo']) {$write_bd['file'] = $arr[$value]['photo'];}
			if ($arr[$value]['video']) {$write_bd['file'] = $arr[$value]['video'];}
			if ($arr[$value]['document']) {$write_bd['file'] = $arr[$value]['document'];}
			if ($arr[$value]['sticker']) {$write_bd['file'] = $arr[$value]['sticker'];}
			if ($arr[$value]['media']) {$write_bd['file'] = json_encode($arr[$value]['media'], true);}
			if ($arr[$value]['act']) {$write_bd['text'] = $arr[$value]['act'];}
			// доп
			if ($arr[$value]['preview']) {$write_bd['preview'] = 1;}
			if ($arr[$value]['no_signal']) {$write_bd['no_signal'] = 1;}
			if ($arr[$value]['no_delete'] OR $no_delete_bot) {$write_bd['no_delete'] = 1;}
			if ($arr[$value]['timer']) {
				if (mb_stripos($arr[$value]['timer'], '+') !== false) {//задали промежуток
					$write_bd['timer'] = date('Y-m-d H:i:s', strtotime($arr[$value]['timer']));
				} else {//задали конкретную дату и время
					$write_bd['timer'] = $arr[$value]['timer'];
				}				
			} else {
				$write_bd['timer'] = '0000-00-00 00:00:00';
			}
			// не сделано
			// question 
			// game_short_name

			// не создавать новое а редактируем старое
			
			if (!$no_old AND in_array($write_bd['type'], ['sendMessage', 'sendPhoto', 'sendVideo'])) {
				$sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_history` WHERE `chat_id` = '$write_bd[chat_id]' AND (`types` = 'sendMessage' OR `types` = 'sendPhoto' OR `types` = 'sendVideo') ORDER BY `id` DESC LIMIT 1");
				while($row = mysqli_fetch_assoc($sql)) {
					$write_bd['edit_message_id'];
				}
			}
			/* if ($chat_id == 5079442067) {
				file_put_contents(__DIR__.'/bot_telegram_send2.txt', $write_bd['keyboard']);
			} */
			$check_message = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_message` WHERE `chat_id` = '$write_bd[chat_id]' AND `types` = '$write_bd[type]' AND `text` = '$write_bd[text]' AND `file` = '$write_bd[file]' AND `keyboard` = '$write_bd[keyboard]' AND `menu` = '$write_bd[menu]' AND `entities` = '$write_bd[entities]' AND `timer` = '$write_bd[timer]' ")); 
			if (!$check_message) {
				mysqli_query($CONNECT, "INSERT INTO `bot_message` (`chat_id`, `types`, `text`, `file`, `keyboard`, `menu`, `entities`, `preview`, `no_signal`, `no_delete`, `edit_message_id`, `timer`) VALUES ('$write_bd[chat_id]', '$write_bd[type]', '$write_bd[text]', '$write_bd[file]', '$write_bd[keyboard]', '$write_bd[menu]', '$write_bd[entities]', '$write_bd[preview]', '$write_bd[no_signal]', '$write_bd[no_delete]', '$write_bd[edit_message_id]', '$write_bd[timer]')");
			} else {
				//file_put_contents(__DIR__.'/repeat.txt', $write_bd['chat_id']);
			}
		}
		$no_old = true; //чтоб только первый прогон мог изменять сообщения
	}	
	$temer_delete = false;

}

?>