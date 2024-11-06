<?php
$set_filmlist['rand_rating'] = 6.8;// минимальный рейтинг фильмов который показываем
$set_filmlist['rand_rating_user_count'] = 200000;// минимум человек учавствовало в рейтинге, которые показываем


$set_filmlist['genre_rating'] = 6.5;// минимальный рейтинг фильмов который показываем
$set_filmlist['genre_rating_user_count'] = 70000;// минимум человек учавствовало в рейтинге, которые показываем

$set_filmlist['top_rating'] = 8.1;// минимальный рейтинг фильмов который показываем
$set_filmlist['top_rating_user_count'] = 250000;// минимум человек учавствовало в рейтинге, которые показываем

function filmlist_time($minute) {
    if ($minute AND is_numeric($minute)) {
        if ($minute >= 60) {
            $hour = floor($minute / 60);
            $dop_minute = $minute - ($hour * 60);
            $text = $hour." ".words($hour, ['час', 'часа', 'часов'])." ".$dop_minute." ".words($dop_minute, ['минута', 'минуты', 'минут']);
        } else {
            $text = $minute." ".words($minute, ['минута', 'минуты', 'минут']);
        }    
        return $text;
    } else {
        return 0;
    }
}
?>