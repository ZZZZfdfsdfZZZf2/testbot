<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

include $back_dir.'/__config.php';
include 'load_config.php';

$genre = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` WHERE `id` = '$user_id'")); 
$sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` WHERE `load_check` = '0' ORDER BY `id` ASC LIMIT 2");
while($row = mysqli_fetch_assoc($sql) ){
    $go = true;
    $genre = $row['num'];
    for ($i = 1; $i <= 20; $i++) {
        echo "</br>================================ genres=".$genre." page=".$i;
        $count = 0;
        $count_all = 0;
        unset($answer);
        $answer = load_filmlist('/api/v2.2/films', ['page' => $i, 'genres' => $genre, 'order' => 'RATING', 'type' => 'FILM', 'ratingFrom' => '6', 'ratingTo' => '7']);
        if ($answer['total'] AND $answer['totalPages'] AND $answer['items']) {//если успех
            foreach($answer['items'] as $value) {
                unset($kinopoiskId, $title, $rating, $photo, $genres, $countries, $year);
                $kinopoiskId = $value['kinopoiskId'];
                $title = $value['nameRu'];
                $title = str_replace('"', '\"', $title); 
                $title = str_replace("'", "`", $title); 
                $rating = $value['ratingKinopoisk'];
                $year = $value['year'];
                $photo = $value['posterUrlPreview'];
                //echo "</br>--------</br>".$kinopoiskId."</br>".$title."</br>".$rating."</br>".$photo."</br>".$year."</br>";
                foreach($value['genres'] as $genre_val) {
                    $load_genre = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` WHERE `name` = '$genre_val[genre]'")); 
                    if ($load_genre) {
                        $genres .= ','.$load_genre['num'].',';
                        //echo " +".$genre_val['genre'];
                    } else {
                        //echo " -".$genre_val['genre'];
                    }
                }
                //echo "</br>".$genres;
                foreach($value['countries'] as $country_val) {
                    $countries .= $country_val['country'].',';
                }
                //echo "</br>".$countries;
                //сохраняем фильм если он выше 6.5 балов в кинопоиске
                $count_all++;
                if ($rating AND is_numeric($rating) AND $rating >= 6 AND $title) {
                    $check_film = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_load` WHERE `kinopoiskId` = '$kinopoiskId'")); 
                    if (!$check_film) {
                        mysqli_query($CONNECT, "INSERT INTO `module_filmlist_load` 
                        (`kinopoiskId`, `title`, `photo`, `genres`, `rating`, `year`, `countries`) VALUES 
                        ('$kinopoiskId', '$title', '$photo', '$genres', '$rating', '$year', '$countries')");
                        $count++;
                    }
                }
            }
            echo " Добавлено: ".$count." из ".$count_all;
        } else {
            $error = $answer;
            $error['error_true'] = true;
            break;
        }

    }
    if (!$error){
        mysqli_query($CONNECT, "UPDATE `module_filmlist_genres` SET `load_check` = '1' WHERE `num` = '$genre'");
    } else {
        echo "</br></br> ОШИБКА</br>";
        if ($answer['status'] == 402) {echo "Превышен лимит запросов(или дневной, или общий)";}
        else if ($answer['status'] == 429) {echo "Слишком много запросов. Общий лимит - 20 запросов в секунду";}
        else if ($answer['status'] == 404) {echo "Фильм не найден";}
        else if ($answer['status'] == 401) {echo "Пустой или неправильный токен";}
        else {
            echo '<pre>';
            print_r($answer);
            echo '</pre>';
            echo '* если нет массива значит: Превышен лимит запросов';
        }        
        break;
    }
}
//премьеры
//$answer = load_filmlist('v2.2/films/premieres', ['year' => '2022', 'month' => 'APRIL']);
if (!$go) {echo 'Парсинг завершен';}
?>