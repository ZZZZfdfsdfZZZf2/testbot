<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

include $back_dir.'/__config.php';
include 'load_config.php';

mysqli_query($CONNECT, "TRUNCATE TABLE `module_filmlist`");
mysqli_query($CONNECT, "TRUNCATE TABLE `module_filmlist_top`");

//обычный список
$sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_load` WHERE `load_check` = '1' AND `rating_user_count` > '50000' ORDER BY `rating` DESC, `rating_user_count` DESC");
while($r = mysqli_fetch_assoc($sql) ){
    $check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist` WHERE `kinopoiskId` = '$r[kinopoiskId]'")); 
    if (!$check) {
        mysqli_query($CONNECT, "INSERT INTO `module_filmlist` 
        (`kinopoiskId`, `title`, `photo`, `genres`, `rating`, `rating_user_count`, `year`, `countries`, `description`, `link`, `film_length`, `load_check`) VALUES 
        ('$r[kinopoiskId]', '$r[title]', '$r[photo]', '$r[genres]', '$r[rating]', '$r[rating_user_count]', '$r[year]', '$r[countries]', '$r[description]', '$r[link]', '$r[film_length]', '$r[load_check]')");
    }    
}
//топ
$sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_load` WHERE `load_check` = '1' AND `rating_user_count` > '200000' AND `rating` > '8' ORDER BY  `rating_user_count` DESC, `rating` DESC LIMIT 100");
while($r = mysqli_fetch_assoc($sql) ){
    $check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_top` WHERE `kinopoiskId` = '$r[kinopoiskId]'")); 
    if (!$check) {
        mysqli_query($CONNECT, "INSERT INTO `module_filmlist_top` 
        (`kinopoiskId`, `title`, `photo`, `genres`, `rating`, `rating_user_count`, `year`, `countries`, `description`, `link`, `film_length`, `load_check`) VALUES 
        ('$r[kinopoiskId]', '$r[title]', '$r[photo]', '$r[genres]', '$r[rating]', '$r[rating_user_count]', '$r[year]', '$r[countries]', '$r[description]', '$r[link]', '$r[film_length]', '$r[load_check]')");
    }    
}
//премьеры
//$answer = load_filmlist('v2.2/films/premieres', ['year' => '2022', 'month' => 'APRIL']);
echo 'ОК';
?>