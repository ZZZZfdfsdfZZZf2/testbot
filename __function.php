<?php
/* 
$text = strip_tags($text); //убирает все теги ,если какие то надо оставить, писать: strip_tags($text, '<p><a>');
$text = htmlspecialchars($text);   // весь код становится текстом 
$text = trim($text); //убираем пробел спреди и сзади

https://t.me/End_Soft
https://t.me/End_Soft
https://t.me/End_Soft
https://t.me/End_Soft
 */

$now = date("Y-m-d H:i:s", strtotime("now"));
$now_date = date("Y-m-d", strtotime("now"));

// setting
$sql = mysqli_query($CONNECT, "SELECT * FROM `setting`");
while($row = mysqli_fetch_assoc($sql) ){
    $setting[$row['name']] = $row['param'];
}

function words($number, $word = false) {// words(1, ['пользователь', 'пользователя', 'пользователей']) или words(1, 'рубль') или words(1, 'рублей')
    if (is_numeric($number)) { 
        if (!is_array($word)){
            $array = [
                ['пользователь', 'пользователя', 'пользователей'],
                ['рубль', 'рубля', 'рублей'],
                ['доллар', 'доллара', 'долларов'],
                ['штука', 'штуки', 'штук'],
                ['балл', 'балла', 'баллов'],
                ['кристалл', 'кристалла', 'кристаллов'],
                ['коин', 'коина', 'коинов'],
                ['день', 'дня', 'дней'] //по дефолту
            ];
            foreach($array as $value) {
                if (in_array($word, $value)) { $word = $value; break; }
            }
            if (!is_array($word)){$word = end($array);}
        }
        $last = substr($number, -1);
        if (($number >= 11 AND $number <= 19) OR in_array($last, [0, 5, 6, 7, 8, 9])) {return $word[2];} 
        else if ($last == 1) {return $word[0];} 
        else if (in_array($last, [2, 3, 4])) {return $word[1];}
    } else {return '';}
}

function text($text, $into = false) {
    if (!$into OR $into == 'bd') {//Чтоб записать в БД, то что пришло из бота или из цуи, сохранив смайлики
        $text = trim($text);
        $text = str_replace(['&lt;', '&gt;', '\/', '<br>', '<\br>'], ['<', '>', '/', "\n", "\n"], $text);
        $text = strip_tags($text, '<b><i><u><s><a><code><pre><tg-spoiler>');
        $text = str_replace(["'"/* , '<', '>' */], ['&prime;'/* , '&lt;', '&gt;' */], $text);
        $text = json_encode($text);
        $text = str_replace(["\\"], [ "\\\\"], $text);
        return $text;
    } else if ($into == 'bot' OR $into == 'input') {//Чтоб выдать из БД  со смайликами и отправить в бот или в input
        $text = json_decode($text);
        $text = str_replace(['<br>', '&prime;'/*, '&lt;', '&gt;', '\/' */], ["\n", "'" /*, '<', '>',  '/'*/], $text);
        $text = strip_tags($text, '<b><i><u><s><a><code><pre><tg-spoiler>');
        return $text;
    } else if ($into == 'web') {//Чтоб выдать из БД со смайликами и отправить на страницу (в гугл хроме смайлы показываются криво, это проблема гугл хрома)
        $text = str_replace(['&prime;', '\n'], ["'", '</br>'], $text);
        $text = json_decode($text);
        return $text;
    } else {
        return 'Ошибка function text';
    }
}

function money($money_full, $cent_off = false){//$cent_off вклюает выключает копейки //&#8381; - знак рубли
    if ($money_full) {
        $money_full = str_replace(',', '.', $money_full); 
        $money = number_format(explode('.', $money_full)[0], 0, '.', ' ');
    } else {
        $money = 0;
    }
    if ($cent_off OR mb_stripos($money_full, '.') !== false) {
        $cent = explode('.', $money_full)[1];
        $cent = mb_substr($cent, 0, 2);
        if (!$cent) {$cent = '00';} else if (mb_substr($cent, 0, 1) != 0 AND mb_substr($cent, 1, 1) == 0) {$cent = $cent.'0';}    
        return $money.'.'.$cent;
    } else {
        return $money;
    }
}

function short($text, $param = 30) {
	$text = preg_replace("/(\?{2,})/", '?', $text);
	if (mb_strlen($text) > $param) {$text = mb_substr($text, 0, $param).'...';}	
    return $text;
}

function color($param) {
    if ($param >= 10 AND $param <= 19) {$param = 'main';}
    else if ($param >= 20 AND $param <= 29) {$param = 'blue';}
    else if ($param >= 30 AND $param <= 39) {$param = 'red';}
    else if ($param >= 40 AND $param <= 49) {$param = 'red';}
    $main = [0, 'main'];
    $main_w = [];
    $blue = ['Accepted', 'PAID', 'blue'];
    $blue_w = [];
    $red = [1, 'red'];
    $red_w = ['ERROR'];
    $orange = ['orange'];
    $orange_w = ['pause'];
    if (in_array($param, $main)) {return ' color_main ';}
    else if (in_array($param, $main_w)) {return ' color_main fw ';}
    else if (in_array($param, $blue)) {return ' color_blue ';}
    else if (in_array($param, $blue_w)) {return ' color_blue fw ';}
    else if (in_array($param, $orange)) {return ' color_orange ';}
    else if (in_array($param, $orange_w)) {return ' color_orange fw ';}
    else if (in_array($param, $red)) {return ' color_red';}
    else if (in_array($param, $red_w)) {return ' color_red fw ';}
    else {return ' color_main ';}
}

function color_online($customer, $minute = 5){// параметр - дата с БД - онлайн пользователь или нет
    if ($customer['ban']) {return ' color_red ';}
    else if (strtotime("-".$minute." minute") <= strtotime($customer['date_online'])) {return ' color_blue ';}
    else {return ' color_main ';}
}

function level($level) {
    if ($level >= 9) {return 'Супер администратор';}
    else if ($level >= 7) {return 'Администратор';}
    else if ($level >= 5) {return 'Модератор';}
    else if ($level >= 3) {return 'Пользователь';}
    else {return 'В бане';}
}


?>