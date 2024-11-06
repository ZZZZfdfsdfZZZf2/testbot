<? 

//СЛИТО В https://t.me/End_Soft
include_once '__config.php';
include_once 'bot_config.php';
echo '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Бот обязательной подписки</title>
</head>
<body>
Бот обязательной подписки версии 12</br></br>Подключенные модули:';
//добавляем колонку note_number_1

// пдгружаем список таблиц
$DB = DB;
$result = mysqli_query($CONNECT, "SHOW TABLES FROM `$DB`");
while ($row = mysqli_fetch_row($result)) {$bd_table[] = $row[0];}

if (!$module) {
	echo '</br></br>Нет ни одного подключенного модуля';
}

foreach($module as $key => $value) {
	$table[$key] = array();
	$col_text[$key] = array();
	$col_int[$key] = array();
	$col_float[$key] = array();
}
//какие таблицы нужны в модулях, которые мы проверяем на наличие, даже ели талбиц нет то должен быть пустой массив
$table['dialog'] = ['module_dialog_couples', 'module_dialog_pay', 'module_dialog_price', 'module_dialog_ref', 'module_dialog_premium'];
$table['filmlist'] = ['module_filmlist', 'module_filmlist_genres', 'module_filmlist_look', 'module_filmlist_top'];
$table['filmshow'] = ['module_filmshow_films'];
$table['filmshowsmart'] = ['module_filmshowsmart_films'];
$table['horoscope'] = ['module_horoscope'];
$table['house'] = ['module_house_price', 'module_house_game'];
$table['mailing'] = ['module_mailling'];
$table['buyout'] = [];
$table['menu'] = [];
$table['money'] = ['module_money_pay_out'];
$table['number'] = ['module_number_films'];
$table['numberlink'] = ['module_numberlink_films'];
$table['numlist'] = ['module_numlist_films'];
$table['onemessage'] = ['module_onemessage'];
$table['rating'] = ['module_rating_pay', 'module_rating_premium', 'module_rating_price', 'module_rating_ref'];
$table['welcome'] = ['module_welcome_channel'];
$table['filmrandom'] = ['module_filmrandom', 'module_filmrandom_genres'];

//какие дополнительные колонки добавляем в таблицу bot_user при подключении модулей
// house
$col_int['house'] = ['house_balance'];
// filmlist
$col_int['filmlist'] = ['filmlist_1', 'filmlist_3'];
$col_text['filmlist'] = ['filmlist_2'];
// rating
$col_int['rating'] = ['rating_sex', 'rating_find_sex', 'rating_age', 'rating_like_sum', 'rating_like_count', 'rating_complaint', 'rating_vip'];
$col_float['rating'] = ['rating_like_grade'];
$col_text['rating'] = ['rating_name', 'rating_file_id', 'rating_file_type', 'rating_desc', 'rating_like_id', 'full_name'];
// buyout
$col_int['buyout'] = ['buyout_balance', 'buyout_residents', 'buyout_resident_dop', 'buyout_game_type', 'buyout_game_rate'];
$col_text['buyout'] = ['buyout_town', 'buyout_ref', 'buyout_ref_top'];
// clicker
$col_int['clicker'] = ['clicker_all', 'clicker_max'];
$col_text['clicker'] = ['clicker_second', 'full_name'];
// dialog
$col_int['dialog'] = ['dialog_save_1', 'dialog_save_2', 'dialog_save_3', 'dialog_pay'];
// button=ref
$col_int['button=ref'] = ['button_ref_count'];

$coloumn_string = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'bot_user'"))[0];
$coloumn_array = explode(',', $coloumn_string);

foreach($module as $key => $v) {
	echo "</br>======================</br>".$key;
	
	// --------------- проверяем наличие таблиц
	unset($er1);	
	foreach($table[$key] as $t) {
		if (!in_array($t, $bd_table)) {
			$er1 .= $t.",";
		}
	}
	if ($er1) {
		echo '</br>------- нет таблиц: '.$er1;
	}
	// --------------- добавляем колонки в главную таблицу bot_user
	unset($er2);	
	foreach($col_text[$key] as $c) {
		if (!in_array($c, $coloumn_array)) {
			mysqli_query($CONNECT, "ALTER TABLE `bot_user` ADD COLUMN `$c` TEXT NOT NULL AFTER `ban`");
			$er2 .= $c.",";
		}			
	}
	foreach($col_int[$key] as $c) {
		if (!in_array($c, $coloumn_array)) {
			mysqli_query($CONNECT, "ALTER TABLE `bot_user` ADD COLUMN `$c` INT NOT NULL AFTER `ban`");
			$er2 .= $c.",";
		}			
	}
	foreach($col_float[$key] as $c) {
		if (!in_array($c, $coloumn_array)) {
			mysqli_query($CONNECT, "ALTER TABLE `bot_user` ADD COLUMN `$c` FLOAT NOT NULL AFTER `ban`");
			$er2 .= $c.",";
		}			
	}
	if ($er2) {
		echo '</br>+++++++++ добавили колонки: '.$er2;
	}
	//$table['clicker'] = ['module_clicker'];
}


echo '
</body>
</html>';
?>