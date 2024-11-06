<?php
$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir2 = implode('/', $back_dir);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

include $back_dir.'/__config.php';
include $back_dir2.'/bot_filmlist_config.php';

$all_count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_filmlist`"))[0];
echo "Всего фильмов в базе:  ".$all_count;

echo "</br></br>============================</br>";
echo "Ограничение в разделе случайные: </br>Минимальный рейтин: ".$set_filmlist['rand_rating']." </br>Минимальное кол-во проголосоваших: ".$set_filmlist['rand_rating_user_count'];
$r_dop_sql = " `rating` > '".$set_filmlist['rand_rating']."' AND `rating_user_count` > '".$set_filmlist['rand_rating_user_count']."' ";
$r_count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_filmlist` WHERE  ".$r_dop_sql))[0];
echo "</br>Из сколько фильмов выдает рандом:  ".$r_count;

echo "</br></br>============================</br>";
echo "Ограничение в разделе по жанрам: </br>Минимальный рейтин: ".$set_filmlist['genre_rating']." </br>Минимальное кол-во проголосоваших: ".$set_filmlist['genre_rating_user_count'];
$s_dop_sql = " `rating` > '".$set_filmlist['genre_rating']."' AND `rating_user_count` > '".$set_filmlist['genre_rating_user_count']."' ";
$s_count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_filmlist` WHERE  ".$s_dop_sql))[0];
echo "</br>Из сколько фильмов выдает жанры:  ".$s_count;
echo "</br></br> По жанрам: (всего фильмов в базе / сколько показываем в по жанрам) ";
$sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` ");
while($row = mysqli_fetch_assoc($sql) ){
    unset($count1, $count2);
    $count1 = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_filmlist` WHERE `genres` LIKE '%,$row[num],%' "))[0];
    $count2 = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_filmlist` WHERE `genres` LIKE '%,$row[num],%' AND ".$s_dop_sql))[0];
    echo '</br>'.$row['name']." - ".$count1." / ".$count2;
}

echo "</br></br>============================</br>";
echo "Ограничение в разделе ТОП: </br>Минимальный рейтин: ".$set_filmlist['top_rating']." </br>Минимальное кол-во проголосоваших: ".$set_filmlist['top_rating_user_count'];
$t_dop_sql = " `rating` > '".$set_filmlist['top_rating']."' AND `rating_user_count` > '".$set_filmlist['top_rating_user_count']."' ";
$t_count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_filmlist` WHERE  ".$t_dop_sql))[0];
echo "</br>Сколько фильмов ТОП:  ".$t_count;
echo "</br></br>ТОП ФИЛЬМЫ:</br><table>";
echo "<tr><td>Название</td><td>Рейтинг</td><td>Кол-во проголосовавших</td><td>Ссылка</td></tr>";
$sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist` WHERE $t_dop_sql ORDER BY `rating_user_count` DESC");
while($row = mysqli_fetch_assoc($sql) ){
    echo '<tr><td>'.$row['title']."</td><td>".$row['rating']."</td><td>".$row['rating_user_count']."</td><td>".$row['link']."</td></tr>";
}
echo "</table>";
?>