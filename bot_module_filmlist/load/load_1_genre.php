<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

include $back_dir.'/__config.php';
include 'load_config.php';

//список жанров и стран
$answer = load_filmlist('/api/v2.2/films/filters');


foreach($answer['genres'] as $value) {
    $name = $value['genre'];
    if ($name AND !in_array($name, ['новости', 'концерт', 'церемония', 'реальное ТВ', 'игра', 'ток-шоу', 'фильм-нуар', 'для взрослых'])) {// исключения
        $check = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` WHERE `num` = '$value[id]'")); 
        if (!$check) {
            $button = $name;
            if ($button == 'история') {$button = "исторический";}
            $smile = [
                "триллер" => "🦹‍♂️",
                "драма" => "😭",
                "криминал" => "🔪",
                "мелодрама" => "👩‍❤️‍👨",
                "детектив" => "🕵️‍♂️",
                "фантастика" => "👽",
                "приключения" => "⛰",
                "биография" => "👤",
                "вестерн" => "🤠",
                "боевик" => "💥",
                "фэнтези" => "🧙‍♀️",
                "комедия" => "🤣",
                "военный" => "⚔",
                "исторический" => "🛡",
                "музыка" => "🎻",
                "ужасы" => "🧟‍♂️",
                "мультфильм" => "👶",
                "семейный" => "👨‍👩‍👦",
                "мюзикл" => "🎼",
                "спорт" => "⚽️",
                "документальный" => "🤓",
                "короткометражка" => "🎥",
                "аниме" => "👧",
                //"для взрослых" => "😜",
                "детский" => "🧒",
            ];
            $button = $smile[$button]." ".ucfirst($button)." ".$smile[$button];// ucfirst() первая буква заглавная
            echo '</br>'.$button;
            $button = text($button);
            mysqli_query($CONNECT, "INSERT INTO `module_filmlist_genres` (`num`, `button`, `name`) VALUES ('$value[id]', '$button', '$name')");
        }
    }
}
echo '<pre>';
print_r($answer['genres']);
echo '</pre>';

?>