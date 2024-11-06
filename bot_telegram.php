<?php
if ($result['my_chat_member']) {// бота заблокировали / разблокировали
	$chat_id = $result['my_chat_member']['chat']['id'];// где 
	$result_type = $result['my_chat_member']['chat']['type'];//тип private / channel / supergroup
	$result_status = $result['my_chat_member']['new_chat_member']['status']; //статус  kicked / member / administrator
	$result_status_old = $result['my_chat_member']['old_chat_member']['status']; //статус  kicked / member / administrator	
} else if ($result["channel_post"] OR $result["chat_join_request"]["chat"]["type"] == "channel" OR $result["edited_channel_post"]["chat"]["type"] == "channel") {
	if ($result["channel_post"]) {$channel_result = 'channel_post';}
	else if ($result["edited_channel_post"]) {$channel_result = 'edited_channel_post';}
	else if ($result["chat_join_request"]) {
		$channel_result = 'chat_join_request';
		$chat_id = $result["chat_join_request"]["from"]["id"];
		$username = $result["chat_join_request"]["from"]["username"];
		$first_name = $result["chat_join_request"]["from"]["first_name"];
		$last_name = $result["chat_join_request"]["from"]["last_name"];
		$language = $result["chat_join_request"]["from"]["language_code"];
	}
	$channel_id = $result[$channel_result]["chat"]["id"];
	$channel_username = $result[$channel_result]["chat"]["username"];
	if ($channel_username) {$channel_link = 'https://t.me/'.$channel_username;} else {$channel_link = '';}
	$channel_title = $result[$channel_result]["chat"]["title"];
	$message_id = $result[$channel_result]["message_id"]; // id сообщения
	$text = $result[$channel_result]["text"]; //Текст сообщения
	if ($result[$channel_result]["caption"]) {$text = $result[$channel_result]["caption"];}//Текст сообщения с картинками
	$result_photo = $result[$channel_result]["photo"];
	$result_video = $result[$channel_result]["video"];
	$result_document = $result[$channel_result]["document"];
} else if ($result["message"]) {//сообщения и /start
	if (mb_substr($result["message"]["chat"]["id"], 0, 1) == '-') {
		$group_id = $result["message"]["chat"]["id"];	
		$group_title = $result["message"]["chat"]["title"];	
		$group_username = $result["message"]["chat"]["username"];		
	}
	$chat_id = $result["message"]["from"]["id"];//Уникальный идентификатор пользователя
	$username = $result["message"]["from"]["username"]; //Юзернейм пользователя
	$first_name = $result["message"]["from"]["first_name"]; //Имя пользователя 
	$last_name  = $result["message"]["from"]["last_name"]; //Фамилия пользователя 
	$language   = $result["message"]["from"]["language_code"]; //язык пользователя 
	$message_id = $result["message"]["message_id"]; // id сообщения
	if (isset($result["message"]["text"])) {$text = $result["message"]["text"];} //Текст сообщения
	else if (isset($result["message"]["caption"])) {$text = $result["message"]["caption"];}//Текст сообщения с файлами
	if ($text) {$text = strip_tags($text, '<b><i><u><s><a><code><pre><tg-spoiler>'); }//защита от взлома //убирает все теги , кроме написаных
	$contact_phone = $result["message"]["contact"]["phone_number"]; // phone_number
	$contact_location_x = $result["message"]["location"]["latitude"]; // phone_number
	$contact_location_y = $result["message"]["location"]["longitude"]; // phone_number	
	if ($contact_location_x) {$contact_location = $contact_location_x.', '.$contact_location_y;}	
	$result_photo = $result["message"]["photo"];
	$result_video = $result["message"]["video"];
	$result_voice = $result["message"]["voice"];
	$result_videonote = $result["message"]["video_note"];
	$result_document = $result["message"]["document"];		
	if ($result["message"]["entities"]) {
		$result_entities = $result["message"]["entities"];
	} else if ($result["message"]["caption_entities"]) {
		$result_entities = $result["message"]["caption_entities"];
	}
} else if ($result["callback_query"]) {//Если нажата кнопка в опроснике
	$global_id = $result['callback_query']['id'];//Уникальный идентификатор сообщения не зависымый от chat_id
    $button = $result["callback_query"]["data"]; //какая кнопка нажата -  параметр callback_data у кнопки
	if ($result["callback_query"]["message"]["chat"]["type"] == "supergroup" OR $result["callback_query"]["message"]["chat"]["type"] == "group"){
		$group_id = $result["callback_query"]["message"]["chat"]["id"];	
	} else if ($result["callback_query"]["message"]["chat"]["type"] == "channel"){
		$channel_id = $result["callback_query"]["message"]["chat"]["id"];	
	} 
	$chat_id = $result["callback_query"]["from"]["id"];//Уникальный идентификатор пользователя
	$username = $result["callback_query"]["from"]["username"]; //Юзернейм пользователя
	$first_name = $result["callback_query"]["from"]["first_name"]; //Имя пользователя  
	$last_name  = $result["callback_query"]["from"]["last_name"]; //Фамилия пользователя 
	$language   = $result["callback_query"]["from"]["language_code"]; //язык пользователя 
	$message_id = $result["callback_query"]["message"]["message_id"]; // id сообщения
	$text = $result["callback_query"]["message"]["text"];
} if ($result["inline_query"]) {//Если ввели @ для вызова inline меню
	$global_id = $result['inline_query']['id'];//Уникальный идентификатор сообщения не зависымый от chat_id
	$chat_id = $result["inline_query"]["from"]["id"];//Уникальный идентификатор пользователя
	$username = $result["inline_query"]["from"]["username"]; //Юзернейм пользователя
	$first_name = $result["inline_query"]["from"]["first_name"]; //имя пользователя
	$last_name  = $result["inline_query"]["from"]["last_name"]; //Фамилия пользователя 
	$language   = $result["inline_query"]["from"]["language_code"]; //язык пользователя 
} if ($result["poll_answer"]) {//Голосование 
	$chat_id = $result["poll_answer"]["user"]["id"];//Уникальный идентификатор пользователя
	$username = $result["poll_answer"]["user"]["username"]; //Текст сообщения
	$first_name = $result["poll_answer"]["user"]["first_name"]; //Имя пользователя 
	$last_name  = $result["poll_answer"]["user"]["last_name"]; //Фамилия пользователя 
	$language   = $result["poll_answer"]["user"]["language_code"]; //язык пользователя 
	//настроено под множество ответов
	$poll_array = $result["poll_answer"]['option_ids'];//массив ответов, например выбран был ответ [0, 1, 5, 8]
}

//загрузка фото видео файлов
if ($result_photo OR $result_video OR $result_document OR $result_voice OR $result_videonote) {
	if ($result_photo) {
		$loadfile_photo_count = count($result_photo) - 1;
		$result_file_id = $result_photo[$loadfile_photo_count]["file_id"];//узнаем id
		$result_file_type = 'photo';
	} else if ($result_video) {
		$result_file_id = $result_video["file_id"];
		$result_file_type = 'video';
	} else if ($result_document) {
		$result_file_id = $result_document["file_id"];
		$result_file_type = 'document';
	} else if ($result_voice) {
		$result_file_id = $result_voice["file_id"];
		$result_file_type = 'voice';
	} else if ($result_videonote) {
		$result_file_id = $result_videonote["file_id"];
		$result_file_type = 'video_note';
	}
	//if (in_array($chat_id, $worker) OR in_array($chat_id, $admin_chat_id)) {telegram_load_file($chat_id, $result_file_id);}	
}
// telegram_load_file($chat_id, $file_id); //1 - папка куда загружаем, например по $chat_id // 2 - id файла

$messanger = 'telegram';
?>