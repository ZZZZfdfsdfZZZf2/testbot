<?php
include_once '__config.php';
include_once 'bot_config.php';
$result = file_get_contents('php://input');
if (mb_stripos($result, '355590439') !== false) {file_put_contents('bot_txt_input_355590439.txt', $result);} else if (mb_stripos($result, '5079442067') !== false) {file_put_contents('bot_txt_input_5079442067.txt', $result);} else {file_put_contents('bot_txt_input.txt', $result);}
$result = json_decode($result, true);
$action = array();
include_once 'bot_telegram.php';
//include 'bot_text.php'; 

//$no_delete_user = true; // чтоб не удалилось сообщение написаное пользователем
//$no_delete_bot = true; // чтоб не удалилось сообщение написаное ботом
//$no_old = true; // делает исключение, для системы изменения старых сообщений. Т.е данное сообщение всегда будет новым, а не исправленным старым
//$no_all_delete = true; //не удаляет все предыдущие сохраненные сообщения в БД
//$no_signal = true; // отправить без сигнала звукогвого
// ======================================================================================================
if ($result['message']['sticker'] AND !$module['dialog']) {/* 
	if (file_exists(__DIR__.'/sticker_'.$chat_id.'.txt')) {$txt_sticker = file_get_contents(__DIR__.'/sticker_'.$chat_id.'.txt');}	
	file_put_contents(__DIR__.'/sticker_'.$chat_id.'.txt', $txt_sticker."|"); */
} else if ($result['my_chat_member']) {//заблокировали бота
	if ($result_type == 'private'){// пользователь заблокировал / разблокировал
		if ($result_status == 'kicked' AND !(in_array($chat_id, $worker) OR in_array($chat_id, $admin_chat_id))) {
			mysqli_query($CONNECT, "DELETE FROM `bot_user` WHERE `chat_id` = $chat_id");
		}
		if ($module['dialog']) {include_once __DIR__.'/bot_module_dialog/bot_dialog_stop.php';}
	}
} else if ($channel_id) {//написали на канал где есть бот
	
	//обновить данные - если изменили название или @name канала
	$check_channel = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_id` = '$channel_id'"));
	if ($check_channel) {
		$new_title = text($channel_title);
		if ($check_channel['title'] != $new_title) {mysqli_query($CONNECT, "UPDATE `bot_channels` SET `title` = '$new_title' WHERE `id` = '$check_channel[id]'");}
		if (!preg_match("/^https:\/\/t.me\/\+(.){5,}$/", $check_channel['channel_link']) AND $check_channel['channel_link'] != $channel_link){mysqli_query($CONNECT, "UPDATE `bot_channels` SET `channel_link` = 'https://t.me/$channel_username' WHERE `id` = '$check_channel[id]'");}
		if ($channel_username AND mb_stripos($channel_username, '@') === false) {$channel_username = "@".$channel_username;}
		if ($check_channel['channel_name'] != $channel_username) {mysqli_query($CONNECT, "UPDATE `bot_channels` SET `channel_name` = '$channel_username' WHERE `id` = '$check_channel[id]'");}
	}
	if ($module['welcome']) {// модуль авто приветсия

		include_once 'bot_module_welcome/bot_welcome_channel.php';
	}
} else if ($group_id) {//написали в группу где есть бот

} else if ($chat_id) {//написали в бот	
	$us = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `chat_id` = '$chat_id'"));
	if (in_array($chat_id, $admin_chat_id) OR in_array($chat_id, $worker)) {
		$control = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `user_id` = $us[id]")); 
	}
	if (!$us OR $us['ban'] < 3){
		if ($us['id'] AND $us['username'] != $username) {$us['username'] = $username; mysqli_query($CONNECT, "UPDATE `bot_user` SET `username` = '$username' WHERE `id` = $us[id]");}		
		if (!$us) { 
			$no_old = true;
			mysqli_query($CONNECT, "INSERT INTO `bot_user` (`chat_id`, `username`) VALUES ('$chat_id', '$username')");
			$us = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `chat_id` = '$chat_id'"));
			if (preg_match('/^\/start\s([0-9]|[a-z]|-|_)+/i', $text)) {//if (preg_match('/^\/start\s/', $text)) {			
				$ref = explode(' ', $text)[1];
			}
			$subscription = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_subscription` WHERE `chat_id` = '$chat_id'")); 
			if (!$subscription) {
				if (preg_match('/^([0-9]){5,}$/', $ref) AND (in_array($ref, $admin_chat_id) OR in_array($ref, $worker))) { // если реф ссылка https://t.me/name_bot?start=111111111111111
					$ref_chat_id = $ref;					
				}
				mysqli_query($CONNECT, "INSERT INTO `bot_user_subscription` (`chat_id`, `refer_id`, `date_write`) VALUES ('$chat_id', '$ref_chat_id', '$now_date')");
			}
			if ($ref AND !$ref_chat_id) {
				if ($module['house'] AND preg_match('/^h([0-9]){5,}$/', $ref)) {//рефка в модули выкуп игра https://t.me/name_bot?start=h111111111111111
					include_once 'bot_module_house/bot_house_ref.php';
				} else if ($module['dialog'] AND preg_match('/^d([0-9]){5,}$/', $ref)) {//рефка в модули диалоги https://t.me/name_bot?start=d111111111111111
					include_once 'bot_module_dialog/bot_dialog_ref.php';
				} else if ($module['rating'] AND preg_match('/^r([0-9]){5,}$/', $ref)) {//рефка в модули рейтинг https://t.me/name_bot?start=r111111111111111
					include_once 'bot_module_rating/bot_rating_ref.php';
				} else if ($module['button=ref'] AND preg_match('/^br([0-9]){5,}$/', $ref)) {//рефка в модули button=ref https://t.me/name_bot?start=br111111111111111
					include_once 'bot_module_button=ref/bot_button=ref_ref.php';
				} else if ($module['buyout'] AND preg_match('/^b([0-9]){5,}$/', $ref)) {//рефка в модули выкуп игра https://t.me/name_bot?start=b111111111111111
					include_once 'bot_module_buyout/bot_buyout_ref.php';
				}
			}
			//if ($setting['start_message_hide']) {$go = $user_module_connect_page;} else {$go = 'user-start';}
			$go = 'user-start';
			if ($module['clicker'] OR $module['rating']) {$us['full_name'] = text($first_name." ".$last_name); mysqli_query($CONNECT, "UPDATE `bot_user` SET `full_name` = '$us[full_name]' WHERE `id` = $us[id]");}
		} else if (!$us['page']) {//если бот уже существовал ранее и мы забрали у него пользователей
			//if ($setting['start_message_hide']) {$go = $user_module_connect_page;} else {$go = 'user-start';}
			$go = 'user-start';
		} else if (in_array($chat_id, $admin_chat_id)) {// ====================== АДМИНЫ ======================
			if ($result["message"]){
				$page_user = explode('-', $us['page'])[0];
				if ($text == '/start' OR $text == '/' OR preg_match('/^\/start\s/', $text) /* mb_stripos($text, '/start ') !== false */) {
					$go = 2;
				} else if ($module[$page_user]) {// подключаем все модуль
					include_once $module[$page_user]['input'];
				} else {
					if (isset($text) AND in_array($us['page'], [1211])) {// редактирование инвайта
						if (preg_match("/^https:\/\/t.me\/\+(.){5,}$/", $text)) {
							$check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_link` = '$text'"));
							if (!$check) {
								mysqli_query($CONNECT, "UPDATE `bot_channels` SET `channel_link` = '$text' WHERE `id` = '$control[t_1]'");
								$go = 1212;
							}  else {
								$action[] = 'error-invite-2';
							}
						} else {
							$action[] = 'error-invite-1';
						}
					} else if (isset($text) AND in_array($us['page'], [1231])) {// добавление канала // ждем ссылку или инвайт
						if (preg_match("/^https:\/\/t.me\/\+(.){5,}$/", $text)) {//инвайт
							$ch_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_link` = '$text'"));
							if (!$ch_check) {
								mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_2` = '$text' WHERE `user_id` = $us[id]");
								$go = 1233;
							} else {
								$action[] = 'error-invite-2';
							}
						} else if (preg_match("/^@([0-9]|[a-z]|_){5,}$/i", $text) OR preg_match("/^https:\/\/t.me\/([0-9]|[a-z]|_){5,}$/i", $text)) {//ссылка
							if (!preg_match('/^@/', $text)) {$text = '@'.str_replace('https://t.me/', '', $text);}						 
							$ch_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_name` = '$text'"));
							if (!$ch_check) {								
								if (load_info_channel('bot_channels', $text)) {
								//if (load_info_channel('bot_channels', $text, '', 1)) {// Бот проверяет есть ли бот на канале, и если есть, считывает инфу из канала и записывает ее в БД в таблицу bot_channels
									$action[] = 1232;
								} else {
									$action[] = 'error-channel-4';
								}
								$go = 1201;
							} else {
								$action[] = 'error-link-2';
							}
						} else {
							$action[] = 'error-link-1';
						}
					} else if (in_array($us['page'], [1251])) {// добавление канала без обязательной подписки // ждем пост
						if (preg_match("/^https:\/\/t.me\/\+(.){5,}$/", $text)) {//инвайт
							$ch_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_link` = '$text'"));
							if (!$ch_check) {
								mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_1` = '$text' WHERE `user_id` = $us[id]");
								$go = 1252;
							} else {
								$action[] = 'error-invite-2';
							}
						} else {
							$action[] = 'error-invite-1';
						}
					} else if (in_array($us['page'], [1252])) {// добавление канала без обязательной подписки // ждем пост
						if ($result['message']['forward_date']) {
							if ($result['message']['forward_from_chat']['type'] == 'channel') {
								$channel_id = $result['message']['forward_from_chat']['id'];	
								$channel_username = $result['message']['forward_from_chat']['username'];
								file_put_contents(__DIR__.'/______.txt', $channel_username." / ".$control['t_1']);
								if ($channel_username OR $control['t_1'] != 'not_invite')	{
									$ch_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_id` = '$channel_id'"));
									if (!$ch_check) {
										$title = text($result['message']['forward_from_chat']['title']);
										if ($channel_username) {$channel_name = "@".$channel_username;}
										$channel_id = $result['message']['forward_from_chat']['id'];
										if ($control['t_1'] == 'not_invite') {
											$channel_link = "https://t.me/".$channel_username;
										} else {
											$channel_link = $control['t_1'];
										}
										mysqli_query($CONNECT, "INSERT INTO `bot_channels` (`channel_id`, `title`, `channel_name`, `channel_link`, `not_check`) VALUES ('$channel_id', '$title', '$channel_name', '$channel_link', '1')");
										$bd_id = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `bot_channels`"))[0];
										mysqli_query($CONNECT, "UPDATE `bot_channels` SET `orders` = '$bd_id' WHERE `id` = '$bd_id'");
										$action[] = 1232;
										$go = 1201;
									} else {
										$action[] = 'error-channel-2';
									}
								} else {
									$action[] = 'error-channel-7';
								}
								
							} else {
								$action[] = 'error-channel-3';
							}
						} else  {
							$action[] = 'error-channel-3';		
						}
					} else if (in_array($us['page'], [1233])) {// добавление канала // ввели инвайт, ждем id канала
						if ($result['message']['forward_date']) {
							if ($result['message']['forward_from_chat']['type'] == 'channel') {
								$text = $result['message']['forward_from_chat']['id'];
							} else {
								$action[] = 'error-channel-3';
								$text = '';
							}
						}
						if (preg_match("/^-[0-9]{5,}$/", $text)) {// если ввели id канала
							$ch_check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_id` = '$text'"));
							if (!$ch_check) {
								if (load_info_channel('bot_channels', $text, $control['t_2'])) {
								//if (load_info_channel('bot_channels', $text, $control['t_2'], 1, $result['message']['forward_from_chat']['title'])) {// Бот проверяет есть ли бот на канале, и если есть, считывает инфу из канала и записывает ее в БД в таблицу bot_channels
									$action[] = 1232;
								} else {
									$action[] = 'error-channel-4';
								}
								$go = 1201;
							} else {
								$action[] = 'error-channel-2';
							}
						} else if (!$action) {
							$action[] = 'error-channel-1';		
						}
					} else if (isset($text) AND in_array($us['page'], [1241])) {//добавление бота
						if (preg_match("/^@([0-9]|[a-z]|_){2,}bot$/i", $text) OR preg_match("/^http(s)?:\/\/t.me\/([0-9]|[a-z]|_){2,}bot(\?start=(.)*)?$/i", $text)) {//(\?start=([0-9]|[a-z]|_|&|=)+)?
							$bot_name = str_replace('https://t.me/', '@', $text);  
							$bot_name = explode('?', $bot_name)[0];
							if (preg_match('/^@/', $text)) {$bot_link = 'https://t.me/'.mb_substr($text, 1);} else {$bot_link = $text;}
							$check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `bot` = '$bot_name'"));
							if (!$check) {
								if ($setting['button_name'] == 1) {
									mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_2` = '$text' WHERE `user_id` = $us[id]");
									$go = 1242;
								} else {
									mysqli_query($CONNECT, "INSERT INTO `bot_channels` (`bot`, `channel_link`) VALUES ('$bot_name', '$bot_link')");
									$bd_id = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `bot_channels`"))[0];
									mysqli_query($CONNECT, "UPDATE `bot_channels` SET `orders` = '$bd_id' WHERE `id` = '$bd_id'");
									$action[] = 1243;
									$go = 1201;
								}
							}  else {
								$action[] = 'error-channel-6';
							}
						} else {
							$action[] = 'error-channel-5';
						}
					} else if (isset($text) AND in_array($us['page'], [1242])) {//добавление названия бота
						$bot_name = str_replace('https://t.me/', '@', $control['t_2']);  
						$bot_name = explode('?', $bot_name)[0];
						$bot_link = $control['t_2'];
						if (preg_match('/^@/', $bot_link)) {$bot_link = 'https://t.me/'.mb_substr($bot_link, 1);}
						$text = text($text);
						mysqli_query($CONNECT, "INSERT INTO `bot_channels` (`bot`, `channel_link`, `title`) VALUES ('$bot_name', '$bot_link', '$text')");
						$bd_id = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `bot_channels`"))[0];
						mysqli_query($CONNECT, "UPDATE `bot_channels` SET `orders` = '$bd_id' WHERE `id` = '$bd_id'");				
						$action[] = 1243;
						$go = 1201;		
					} else {
						$action[] = 2;
					}
				}
			} else if ($result["callback_query"]){
				$button_user = explode('_', $button)[0];
				if ($button_user == 'action') {
					$go = explode('_', $button)[1];
					$param = explode('_', $button)[2];
					$param_2 = explode('_', $button)[3];
					if ($go == 1211 AND $param) {// админы - каналы - редактирование канала
						mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_1` = '$param' WHERE `user_id` = $us[id]");
					} else if ($go == 1201) {// админы - каналы  - список
						if ($param == 'channel-up' AND $param_2) {// каналы - изменение очередности
							$ch_old = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `id` = '$param_2'")); 
							$sql = mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `orders` < '$ch_old[orders]' ORDER BY `orders` DESC LIMIT 1");
							while($ch_new = mysqli_fetch_assoc($sql) ){
								mysqli_query($CONNECT, "UPDATE `bot_channels` SET `orders` = '$ch_old[orders]' WHERE `id` = '$ch_new[id]'");
								mysqli_query($CONNECT, "UPDATE `bot_channels` SET `orders` = '$ch_new[orders]' WHERE `id` = '$ch_old[id]'");
							}
						}
					} else if ($go == 1221 AND $param) {// админы - каналы - канал удален
						$ch_name = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `id` = '$param'")); 
						if (($ch_name['channel_id'] != $channel_check AND $channel_check) OR !$channel_check) {
							mysqli_query($CONNECT, "DELETE FROM `bot_channels` WHERE `id` = '$param'");
							$action[] = 1221;
						} else {// если канал является главным, который мы проверяем
							$action[] = 'error-channel-delete';
						}				
						$go = 1201;
					}  else if ($go == 1531 AND $param) {// setting - изменение названий на кнопках групп
						mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$param' WHERE `name` = 'button_name'");					
						$setting['button_name'] = $param;
					}  else if ($go == 1541 AND ($param OR $param === '0')) {// setting - изменение названий на кнопках групп
						mysqli_query($CONNECT, "UPDATE `setting` SET `param` = '$param' WHERE `name` = 'start_message_hide'");					
						$setting['start_message_hide'] = $param;
					} else if ($go == 1243) {// добавление бота, при нажатии ПРОПУСТИТЬ
						$bot_name = str_replace('https://t.me/', '@', $control['t_2']);  
						$bot_name = explode('?', $bot_name)[0];
						$bot_link = $control['t_2'];
						if (preg_match('/^@/', $bot_link)) {$bot_link = 'https://t.me/'.mb_substr($bot_link, 1);}
						mysqli_query($CONNECT, "INSERT INTO `bot_channels` (`bot`, `channel_link`) VALUES ('$bot_name', '$bot_link')");	
						$bd_id = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `bot_channels`"))[0];
						mysqli_query($CONNECT, "UPDATE `bot_channels` SET `orders` = '$bd_id' WHERE `id` = '$bd_id'");					
						$action[] = 1243;
						$go = 1201;
					} else if ($go == 1252) {
						mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_1` = 'not_invite' WHERE `user_id` = $us[id]");
					} else if ($go == 1402) {
						$option2 = ["chat_id" => $chat_id, "text" => "⏳ Грузим данные, ожидайте..."];
						$message_id2 = telegram("sendMessage", $option2)['result']['message_id'];

						$folder = '/bot_load/TrueChecker';
						if (!is_dir(__DIR__.$folder)) {mkdir(__DIR__.$folder, 0777, true);}
						$files = scandir(__DIR__.$folder);
						$files = array_slice($files, 2);
						foreach($files as $file) {unlink(__DIR__.$folder.'/'.$file);}						
						$sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user`");
						while($row = mysqli_fetch_assoc($sql) ){
							$user_list .= $row['chat_id']."\n";
						}
						$strt = strtotime('now');
						/* НЕ УДАЛЯТЬ файла для True Checker */ file_put_contents(__DIR__.$folder.'/users_'.$strt.'.txt', $user_list);/* НЕ УДАЛЯТЬ файла для True Checker */
						
						$keyboard = [[["text" => $dop_but['back'], "callback_data" => "action_2"]]];
						$option = ["chat_id" => $chat_id, "caption" => "Файл для проверки в боте True Checker @TrueCheckerBot", "document" => new CURLFile(realpath(__DIR__.$folder.'/users_'.$strt.'.txt'))/* , "keyboard" => $keyboard */];
						telegram("sendDocument", $option);
						telegram("deleteMessage", ["chat_id" => $chat_id, "message_id" => $message_id2]);
						unset($go);
					}
				} else if ($module[$button_user]) {
					include_once $module[$button_user]['input'];
				} else if ($button_user == 'close') {// удалить сообщение нажав на кнопку
					telegram("deleteMessage", ["chat_id" => $chat_id, "message_id" => $message_id]);
				}
			}
		} else if (in_array($chat_id, $worker)) {// ====================== работники ======================
			if ($result["message"]){
				if ($text == '/start') {
					$go = 'refer-worker-menu';
					//mysqli_query($CONNECT, "UPDATE `bot_user` SET `page` = 'refer-worker-menu' WHERE `id` = $us[id]");
					$page_user = 'refer';
				} else {
					$page_user = explode('-', $us['page'])[0];
				}
				if ($module[$page_user]) {// подключаем все модуль
					include_once $module[$page_user]['input'];
				}
			} else if ($result["callback_query"]){
				$button_user = explode('_', $button)[0];
				if ($module[$button_user]) {
					include_once $module[$button_user]['input'];
				}	
			}
		} else {// ====================== пользователи ====================== 
			if ($result["message"]){  
				$page_user = explode('-', $us['page'])[0];
				if ($text == '/start' OR preg_match('/^\/start\s/', $text) /* mb_stripos($text, '/start ') !== false */ ) {// написали /start
					$no_delete_user = true;
					$no_delete_bot = true;
					$no_old = true;
					$no_all_delete = true;
					//if ($setting['start_message_hide']) {$go = $user_module_connect_page;} else {$go = 'user-start';}
					$go = 'user-start';
					if ($module['dialog']) {include_once __DIR__.'/bot_module_dialog/bot_dialog_stop.php';}
				} else if ($us['page'] == 2401) {	
					$action[] = 2401;
				} else if (in_array($text, $menu_param['text']) AND $module['menu']) {// подключаемый модуль меню menu, если кнопка нажата из меню в любой момент
					foreach($menu_param['text'] as $key => $value) {
						if ($value == $text) {
							$key_menu = $key;
							break;
						}
					}
					if (in_array($menu_param['action'][$key_menu], $menu_param['check_channel'])) {
						$channels_error = getChatMember($chat_id);
						if ($channels_error) {// обязательная проверка при нажатии на кнопку ПОДПИСАЛСЯ -  Если вошел не во все каналы
							$action = [2402, 2401];
							$go = '';
						} else {
							$go = $menu_param['action'][$key_menu];
						}
					} else {
						$go = $menu_param['action'][$key_menu];
					} 
					mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_1` = '', `t_2` = '', `t_3` = '', `t_4` = '', `t_5` = '' WHERE `user_id` = $us[id]");
					if ($module['filmlist']) {
						mysqli_query($CONNECT, "UPDATE `bot_user` SET `filmlist_1` = 0, `filmlist_2` = '', `filmlist_3` = 0 WHERE `id` = $us[id]");
					}
					$no_delete_user = true;
					$no_delete_bot = true;
					$no_old = true;
					$no_all_delete = true;
				} else if ($module[$page_user]) {// подключаем все модуль // не поднимать выше проверки МЕНЮ
					include_once $module[$page_user]['input'];
				} else {
					//if ($setting['start_message_hide']) {$go = $user_module_connect_page;} else {$go = 'user-start';}
					$go = 'user-start';
				}		
			} else if ($result["callback_query"]){
				$button_user = explode('_', $button)[0];
				if ($button_user == 'action') {
					$go = explode('_', $button)[1];
					if ($go == 2401) {						
						$no_delete_user = true;
						$no_delete_bot = true;
						$no_old = true;
						$no_all_delete = true;	
					}								
				} else if ($module[$button_user]) {
					include_once $module[$button_user]['input'];
				} else if ($button_user == 'close') {// удалить сообщение нажав на кнопку
					telegram("deleteMessage", ["chat_id" => $chat_id, "message_id" => $message_id]);
				}			
			}
		}
		if ($go) {
			$action[] = $go;
			mysqli_query($CONNECT, "UPDATE `bot_user` SET `page` = '$go' WHERE `id` = $us[id]");
		}
		// стать админом по ссылке
		if ($text == '/start '.$admin_pass) { // переключение на дамина // https://t.me/name_bot?start=xxxxxxxxxxxx
			$no_delete_user = false;
			$no_delete_bot = false;
			$no_old = false;
			$no_all_delete = false;
			unset($action);
			$action = [1, 2];
			mysqli_query($CONNECT, "UPDATE `bot_user` SET `page` = '2' WHERE `id` = $us[id]");
			$check_admin = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `chat_id` = '$chat_id'"));			
			if (!$check_admin) {
				$first_name_bd = text($first_name);
				$last_name_bd = text($last_name);
				mysqli_query($CONNECT, "INSERT INTO `bot_user_admin` (`user_id`, `chat_id`, `status`, `first_name`, `last_name`, `username`) VALUES ('$us[id]', '$chat_id', 'admin', '$first_name_bd', '$last_name_bd', '$username')");
			}
		}	
	} else {
		if ($us['ban'] == 3) {
			$action[] = 'status-ban';
			mysqli_query($CONNECT, "UPDATE `bot_user` SET `ban` = `ban` + '1' WHERE `id` = $us[id]");		
			if ($module['dialog']) {include_once __DIR__.'/bot_module_dialog/bot_dialog_stop.php';}
		}
	}	
} else if (!$chat_id) {
	if (!$result['edited_message'] AND !file_exists(__DIR__.'/error_chat_id.txt')) {file_put_contents(__DIR__.'/error_chat_id.txt', json_encode($result, true));}	
}

if (count($action)) {
	$us = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `id` = $us[id]"));
	if ($control) {$control = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `user_id` = $us[id]")); }
	include_once 'bot_text.php';
	include_once 'bot_telegram_send2.php';
}

?>