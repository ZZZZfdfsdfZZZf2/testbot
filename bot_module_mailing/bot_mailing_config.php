<?php
function mailing_text($text, $into = false) {
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

function mailing_timer($date_1, $date_2, $dif = false) {
    if ($date_1 AND $date_2) {
        $date_1 = strtotime($date_1);
        $date_2 = strtotime($date_2); 
        $sec = $date_2 - $date_1;
        if (!$dif){   
            if ($sec >= 60) {
                $minute = floor($sec / 60);
                if ($minute >= 60) {
                    $hour = floor($minute / 60);
                    if ($hour >= 24) {
                        $day = floor($hour / 24);
                        $dop_hour = $hour - ($day * 24);
                        $text = $day." ".words($day, ['день', 'дня', 'дней'])." ".$dop_hour." ".words($dop_hour, ['час', 'часа', 'часов']);
                    } else {
                        $dop_minute = $minute - ($hour * 60);
                        $text = $hour." ".words($hour, ['час', 'часа', 'часов'])." ".$dop_minute." ".words($dop_minute, ['минута', 'минуты', 'минут']);
                    }
                } else {
                    $text = $minute." ".words($minute, ['минута', 'минуты', 'минут']);
                }
            } else {
                $text = $sec." ".words($sec, ['секунда', 'секунды', 'секунд']);
            }
        } else {
            $text = $sec;
        }
        return $text;
    }
}

?>