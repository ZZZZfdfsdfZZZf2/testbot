<?php

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

include $back_dir.'/__config.php';
include 'load_config.php';

$sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_load` WHERE `load_check` = '0' ORDER BY `id` ASC LIMIT 200");
while($row = mysqli_fetch_assoc($sql) ){
    $go = true;
    
    echo "/ ".$row['id'];
    //echo "</br>--------</br> id =".$row['id']."</br>kinopoiskId =".$row['kinopoiskId'];
    $count = 0;
    $count_all = 0;
    unset($answer);
    $answer = load_filmlist('/api/v2.2/films/'.$row['kinopoiskId']);

    if ($answer['kinopoiskId']) {//если успех
        $link = $answer['webUrl'];
        $rating_user_count = $answer['ratingKinopoiskVoteCount'];
        $filmLength = $answer['filmLength'];
        $description = $answer['description'];
        $description = str_replace('"', '\"', $description); 
        $description = str_replace("'", "`", $description); 
        //echo '</br>'.$link.'</br>'.$rating_user_count.'</br>'.$filmLength.'</br>'.$description;
        mysqli_query($CONNECT, "UPDATE `module_filmlist_load` SET `link` = '$link', `rating_user_count` = '$rating_user_count', `description` = '$description', `film_length` = '$filmLength', `load_check` = '1' WHERE `id` = '$row[id]'");
        $count++;
    } else {
        $error = $answer;
        $error['error_true'] = true;
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