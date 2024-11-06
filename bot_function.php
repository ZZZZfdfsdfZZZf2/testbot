<?php

// ==============================================================================================
//автоматическое подключение модулей

// проверка, присутствует ли основной канал в списке каналов, если нет то тариф 3, и проверка канала на юзеров отсутствует
if ($channel_check) {
	$table_channel_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_id` = '$channel_check'"))['id'];
}

// кол-во каналов в обязательной подписке
$count_bot_channels = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_channels`"))[0];

//============= ПОДГРУЗКА МОДУЛЕЙ ВСЕХ СРАЗУ, ИЩЕМ ВСЕ ПАПКИ С МОДУЛЯМИ
$folder_module = scandir(__DIR__.'/');
$folder_module = array_slice($folder_module, 2);

$module = array();
foreach($folder_module as $key => $value) {
	//отбираем только папки модулей
	if (mb_stripos($value, '.') !== false) {unset($folder_module[$key]);}
	else if (mb_stripos($value, 'graph') !== false) {unset($folder_module[$key]);}
	else if (mb_stripos($value, 'bot_files') !== false) {unset($folder_module[$key]);}
	else if (mb_stripos($value, 'bot_load') !== false) {unset($folder_module[$key]);}
	else {
		//перебираем модули и подключаем
		if ((!$qiwi_token OR !$qiwi_number) AND $value == 'bot_module_money') {continue;} // bot_module_money исключение, если нет qiwi кошелька не работает		
		$name_mod = explode('_', $value)[2];
		
		$module[$name_mod]['input'] = __DIR__.'/'.$value.'/bot_'.$name_mod.'_input.php';
		if ($value == 'bot_module_menu') {continue;} // bot_module_menu исключение, нет личного bot_menu_text.php
		$module[$name_mod]['text'] = __DIR__.'/'.$value.'/bot_'.$name_mod.'_text.php';		
	}
}
if ($module['menu']) {$module_menu_input = $module['menu'];}//так как у старых ботов $module_menu_input в конфиге написано


if ($setting['start_message_hide']) {
	foreach($module as $key => $value) {
		if ($key == 'menu') {
			$user_module_connect_page = "menu";
		} else if (in_array($key, ['welcome', 'mailing', 'money', 'refer'])) {// доп. модули которые не имею запуска после обязательной проверки
		} else if (!$user_module_connect_page) {
			$user_module_connect_page = $key."-go";
		}
	}
}
// ==============================================================================================
$website_file = $website.'/bot_files/';
$folder_file = __DIR__.$website_folder.'/bot_files/';
$folder_bot_files = '/bot_files/';

//список работников и админов
$admin_chat_id = array();
$worker = array();
$sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin`");
while($row = mysqli_fetch_assoc($sql) ){
	if ($row['status'] == 'admin') {
		$admin_chat_id[] = $row['chat_id'];
	} else if ($row['status'] == 'worker') {
		$worker[] = $row['chat_id'];
	}	
}

// ==============================================================================================
function telegram($type, $option = false, $telegram_bot_token = false) {
	if (!$telegram_bot_token){ global $telegram_bot_token; }	
	if (in_array($type, ['sendMessage', 'copyMessage', 'sendPhoto', 'sendAudio', 'sendDocument', 'sendVideo', 'sendAnimation', 'sendVoice', 'editMessageText', 'editMessageCaption']) AND !$option['entities']) {$option['parse_mode'] = "HTML";}//все теги из html
	if (in_array($type, ['sendMessage', 'editMessageText']) AND !$option['preview']) {$option['disable_web_page_preview'] = true;} else {$option['disable_web_page_preview'] = false;} //выключен показ ссылок картинкой сайта
	unset($option['preview']);
	if ($option['keyboard']){
		$keyboard = json_encode(["inline_keyboard" => $option['keyboard']]);
		$option['reply_markup'] = $keyboard;
		unset($option['keyboard']);
	} else if ($option['menu']){
		$keyboard = json_encode(["keyboard" => $option['menu'], "resize_keyboard" => true]);
		$option['reply_markup'] = $keyboard;
		unset($option['menu']);
	}
	if ($type == 'sendMediaGroup'){
		$files = $option['media'];
		unset($option['media']);
		foreach($files as $key => $file) {
			if (mb_stripos($file, 'jpg') !== false OR mb_stripos($file, 'png') !== false) {$media_type = 'photo';}
			else if (mb_stripos($file, 'mp4') !== false) {$media_type = 'video';}
			if ($key == 0) {
				$media[0] = ["type" => $media_type, "media" => $file, "parse_mode" => "HTML"];
				if ($option['text']) { $media[0]["caption"] = $option['text']; }
			} else {
				$media[] = ["type" => $media_type, "media" => $file];
			}				
		}
		$option['media'] = json_encode($media);
		unset($option['text']);			
	} else if ($type == 'editMessageMedia'){
		if ($option['photo']) {$media_type = 'photo'; $option['media'] = $option['photo'];} 
		else if ($option['video']) {$media_type = 'video'; $option['media'] = $option['video'];}
		else if ($option['media']) {
			if (mb_stripos($option['media'], 'jpg') !== false OR mb_stripos($option['media'], 'png') !== false) {$media_type = 'photo';}
			else if (mb_stripos($option['media'], 'mp4') !== false) {$media_type = 'video';}
		}
		$option['media'] = json_encode(["type" => $media_type, "media" => $option['media'], "caption" => $option['text'], "parse_mode" => "HTML"]);
		unset($option['text'], $option['photo'], $option['video']);
	}
	if ($option['entities']) {	
		if ($option['caption']) {
			$option['caption_entities'] = json_encode($option['entities'], true);
			unset($option['entities']);
		} else {
			$option['entities'] = json_encode($option['entities'], true);
		}	
	}
	//$option['protect_content'] = true; // не дает репостить и сохранять
	$zapros = curl_init();
	curl_setopt_array(
		$zapros,
		array(
			CURLOPT_URL => 'https://api.telegram.org/bot'.$telegram_bot_token.'/'.$type,
			CURLOPT_RETURNTRANSFER => TRUE, 
			CURLOPT_TIMEOUT => 10, 
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $option,
		)
	);
	$answer = curl_exec($zapros);
	curl_close($zapros);
	if ($type != 'deleteMessage' AND $type != 'getChatMember' AND  in_array($option['chat_id'], [355590439, 5069727688])){
		file_put_contents('bot_txt_telegram_option.txt', json_encode( $option));
		file_put_contents('bot_txt_telegram_answer.txt', $answer);
	}
	$answer = json_decode($answer, true);	
	return $answer;	
}

/* 
function multi_telegram($type, $option, $array_chat_id) {
    global $telegram_bot_token; 

    if (in_array($type, ['sendMessage', 'copyMessage', 'sendPhoto', 'sendAudio', 'sendDocument', 'sendVideo', 'sendAnimation', 'sendVoice', 'editMessageText', 'editMessageCaption']) AND !$option['entities']) {$option['parse_mode'] = "HTML";}//все теги из html
	if (in_array($type, ['sendMessage', 'editMessageText']) AND !$option['preview']) {$option['disable_web_page_preview'] = true;} else {$option['disable_web_page_preview'] = false;} //выключен показ ссылок картинкой сайта
	unset($option['preview']);
	if ($option['keyboard']){
		$keyboard = json_encode(["inline_keyboard" => $option['keyboard']]);
		$option['reply_markup'] = $keyboard;
		unset($option['keyboard']);
	} else if ($option['menu']){
		$keyboard = json_encode(["keyboard" => $option['menu'], "resize_keyboard" => true]);
		$option['reply_markup'] = $keyboard;
		unset($option['menu']);
	}
	if ($type == 'sendMediaGroup'){
		$files = $option['media'];
		unset($option['media']);
		foreach($files as $key => $file) {
			if (mb_stripos($file, 'jpg') !== false OR mb_stripos($file, 'png') !== false) {$media_type = 'photo';}
			else if (mb_stripos($file, 'mp4') !== false) {$media_type = 'video';}
			if ($key == 0) {
				$media[0] = ["type" => $media_type, "media" => $file, "parse_mode" => "HTML"];
				if ($option['text']) { $media[0]["caption"] = $option['text']; }
			} else {
				$media[] = ["type" => $media_type, "media" => $file];
			}				
		}
		$option['media'] = json_encode($media);
		unset($option['text']);			
	} else if ($type == 'editMessageMedia'){
		if ($option['photo']) {$media_type = 'photo'; $option['media'] = $option['photo'];} 
		else if ($option['video']) {$media_type = 'video'; $option['media'] = $option['video'];}
		else if ($option['media']) {
			if (mb_stripos($option['media'], 'jpg') !== false OR mb_stripos($option['media'], 'png') !== false) {$media_type = 'photo';}
			else if (mb_stripos($option['media'], 'mp4') !== false) {$media_type = 'video';}
		}
		$option['media'] = json_encode(["type" => $media_type, "media" => $option['media'], "caption" => $option['text'], "parse_mode" => "HTML"]);
		unset($option['text'], $option['photo'], $option['video']);
	}
	if ($option['entities']) {	
		if ($option['caption']) {
			$option['caption_entities'] = json_encode($option['entities'], true);
			unset($option['entities']);
		} else {
			$option['entities'] = json_encode($option['entities'], true);
		}	
	}

    $multi = curl_multi_init();
    $handles = array();
    foreach($array_chat_id as $chat_id) {    
        $ch = curl_init();
        unset($option_full);
        $option_full = $option;
        $option_full['chat_id'] = $chat_id;
        $arr = [
            CURLOPT_URL => 'https://api.telegram.org/bot'.$telegram_bot_token.'/'.$type,
            CURLOPT_RETURNTRANSFER => TRUE, 
            CURLOPT_TIMEOUT => 10, 
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $option_full,
        ]; 
        curl_setopt_array($ch, $arr);
        curl_multi_add_handle($multi, $ch);
        $handles[$chat_id] = $ch;
    }
    //обработчик
    do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active && $mrc == CURLM_OK) {if (curl_multi_select($multi) == -1) {usleep(100);}do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);}
    //сохранение
    foreach($handles as $channel) {
        $z++;
        $answer = curl_multi_getcontent($channel);
        curl_multi_remove_handle($multi, $channel);
        file_put_contents(__DIR__.'/cron2_option.txt', json_encode($option_full, true));
        file_put_contents(__DIR__.'/cron2_answer.txt', $answer);
        $answer = json_decode($answer, true);
        $result[] = $answer;
    }
    return $result;
} */

function telegram_load_file($folder, $file_id) {//1 - папка куда загружаем, например по $chat_id // 2 - id файла
	global $telegram_bot_token;
	if (!is_dir(__DIR__.'/bot_load')) {mkdir(__DIR__.'/bot_load', 0777, true);}
	if (!is_dir(__DIR__.'/bot_load/'.$chat_id)) {mkdir(__DIR__.'/bot_load/'.$chat_id, 0777, true);}
	$loadfile_path = telegram("getFile", ["file_id" => $file_id]);//узнаем адрес
	$loadfile_load = file_get_contents('https://api.telegram.org/file/bot'.$telegram_bot_token.'/'.$loadfile_path['result']['file_path']);//скачиваем
	$loadfile_name = explode('/', $loadfile_path['result']['file_path'])[1];
	//file_put_contents(__DIR__.'/bot_load/'.$chat_id.'/'.$loadfile_name, $loadfile_load); //сохранение файла лучше прописать именно с разрешенной страницы page  в файле input
	return $loadfile_name; // получаем ссылку на  файл, для дальнейшего переноса на свой хост
}
// ==============================================================================================


//проверить есть ли юзер на каналах 
// if (getChatMember($chat_id)) {/* пользователь вышел, subscription_now = 0 уже сделано */} else {/* пользователь на каналах */}
function getChatMember($chat_id) {//проверить есть ли юзер на каналах
	global $CONNECT;
	$channel_not = array();
	$option = array();
	$sql = mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `bot` = '' AND (`not_check` = '0' OR `not_check` = '')");
	while($row = mysqli_fetch_assoc($sql) ){	
		unset($find);
		if ($row['channel_id']) {$find = $row['channel_id'];} 
		else if ($row['channel_name']) {$find = $row['channel_name'];}
		if ($find) {$option[] = ["user_id" => $chat_id, "chat_id" => $find];}		
	}
	if (count($option)) {
		$type = "getChatMember";
		global $telegram_bot_token;	
		$multi = curl_multi_init();
		$handles = array();
		foreach($option as $op_k => $option_one) {    
			$ch = curl_init();
			$arr = [
				CURLOPT_URL => 'https://api.telegram.org/bot'.$telegram_bot_token.'/'.$type,
				CURLOPT_RETURNTRANSFER => TRUE, 
				CURLOPT_TIMEOUT => 10, 
				CURLOPT_POST => TRUE,
				CURLOPT_POSTFIELDS => $option_one,
			]; 
			curl_setopt_array($ch, $arr);
			curl_multi_add_handle($multi, $ch);
			$handles[] = $ch;
		}
		do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		while ($active && $mrc == CURLM_OK) {if (curl_multi_select($multi) == -1) {usleep(100);}do {$mrc = curl_multi_exec($multi, $active);} while ($mrc == CURLM_CALL_MULTI_PERFORM);}
		foreach($handles as $key => $val) {
			$answer = curl_multi_getcontent($val);
			curl_multi_remove_handle($multi, $val);
			$answer = json_decode($answer, true);
			if (!in_array($answer['result']['status'], ['member', 'creator', 'administrator'])) {
				$channel_not[] = $option[$key]["chat_id"];
			}			
			if ($answer['description']) {
				file_put_contents(__DIR__.'/_getChatMember_ERROR_'.$find.'.txt', json_encode($answer, true));
			}
			$result[] = $answer;
		}
	}
	if ($channel_not) {		
		mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = '0' WHERE `chat_id` = '$chat_id'");	
	}
	/* if ($chat_id == 5079442067) {
		file_put_contents(__DIR__.'/_getChatMember_option.txt', json_encode($option, true));
		file_put_contents(__DIR__.'/_getChatMember_answer.txt', json_encode($result, true));
		file_put_contents(__DIR__.'/_getChatMember_channelnot.txt', json_encode($channel_not, true));
	} */
	return $channel_not;	
}

function send_admin($message = 'Текст не введен', $main = false) {
	global $admin_chat_id;
	foreach($admin_chat_id as $one) {
		if ($main AND $one == 355590439) {continue;}
		$option = ["chat_id" => $one, "text" => $message];
		telegram("sendMessage", $option);
	}
}


function referal_count_name($param) {
    if ($param == 1) {return "Запустили бот";}
    else if ($param == 2) {return "Подписались на старте";}
    else if ($param == 3) {return "Подписаны до сих пор";}
}

function load_info_channel($table, $channel, $channel_link = false) { // подгружает по @name или id канала информацию о канале, // (таблица БД куда записатб результат, @name/id, инвайт)
	global $CONNECT;	
	global $bot_name;
	$getChatAdmin = telegram("getChatAdministrators", ["chat_id" => $channel]);// проверем является бот админом на канале, и дали ли ему права - добавлять пользователей
	foreach($getChatAdmin['result'] as $value) {
		if ( $value['user']['username'] == $bot_name AND $value['can_invite_users']) { $bot_in_channel = true; break; }
	}
	if ($bot_in_channel) {
		$answer = telegram("getChat", ["chat_id" => $channel]);// выгружаем инфу о канале
		if ($answer['ok']) {
			mysqli_query($CONNECT, "INSERT INTO `$table` (`channel_name`) VALUES ('$channel')");
			$bd_id = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `$table`"))[0];
			mysqli_query($CONNECT, "UPDATE `$table` SET `orders` = '$bd_id' WHERE `id` = '$bd_id'");
			$channel_id = $answer["result"]["id"];
			$title = text($answer["result"]["title"]);
			if ($answer["result"]["username"]) {
				$channel_name = "@".$answer["result"]["username"];						
				if (!$channel_link) {$channel_link = "https://t.me/".$answer["result"]["username"];} 
			}		
			mysqli_query($CONNECT, "UPDATE `$table` SET `channel_id` = '$channel_id', `title` = '$title', `channel_name` = '$channel_name', `channel_link` = '$channel_link' WHERE `id` = '$bd_id'");
			return 1;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}
?>