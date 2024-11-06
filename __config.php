<?php
// СЛИТО В https://t.me/End_Soft
define ('HOST', 'localhost');
define ('USER', 'number');
define ('DB'  , 'number');
define ('PASS', 'vA6gH6oP2k');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);//выводит адекватное описание ошибки у БД
$CONNECT = mysqli_connect(HOST, USER, PASS, DB);
if (!$CONNECT){die ('БАЗА ДАННЫХ НЕ ПОДКЛЮЧЕНА!');}
mysqli_set_charset($CONNECT, "utf8");


$website_folder = ''; //дописать сюда /folder, если код находится не в главной папке
$website = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'].$website_folder;
$website_short = $_SERVER['HTTP_HOST'];

include_once '__function.php';

?>