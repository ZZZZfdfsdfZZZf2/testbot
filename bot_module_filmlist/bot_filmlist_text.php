<?php
include_once 'bot_filmlist_config.php';

if (!$dop_but['back']) { $dop_but['back'] = "⬅️ Назад";}

if (in_array('filmlist-go', $action)) {
    $arr['filmlist-go']["text"] = "Выбор фильмов:";/* 
    $arr['filmlist-go']["menu"][] = ["🎲 Случайный фильм"];
    $arr['filmlist-go']["menu"][] = ["🎬 Фильмы по жанру"];
    $arr['filmlist-go']["menu"][] = ["🏆 Популярные фильмы"]; 
    if ($menu_back) {$arr['filmlist-go']["menu"][] = [$menu_back];} // если есть модуль  меню menu то добавляем кнопку назад*/
    $arr['filmlist-go']["keyboard"][] = [["text" => "🎲 Случайный фильм", "callback_data" => "filmlist_filmlist-random"]];
    $arr['filmlist-go']["keyboard"][] = [["text" => "🎬 Фильмы по жанру", "callback_data" => "filmlist_filmlist-genre-list"]];
    $arr['filmlist-go']["keyboard"][] = [["text" => "🏆 Популярные фильмы", "callback_data" => "filmlist_filmlist-top-list_1"]]; // 1 чтоб сбросить страницы
}
// случайный фильм или показ фильма
if (in_array('filmlist-random', $action) OR in_array('filmlist-show', $action) OR in_array('filmlist-top', $action)) {
    if (in_array('filmlist-random', $action)) {
        $filmlist_name = "filmlist-random";
        $film_page["text"] = "🎲 Случайный фильм: ";
        $sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist` WHERE `rating` > '$set_filmlist[rand_rating]' AND `rating_user_count` > '$set_filmlist[rand_rating_user_count]' ORDER BY `id`");
        while($row = mysqli_fetch_assoc($sql)){
            $filmlist[] = $row;     
            $count++;   
        }
        $keys = array_rand($filmlist);
        $film = $filmlist[$keys];
        unset($filmlist);
        $filmlist_back = [["text" => $dop_but['back'], "callback_data" => "menu_menu"]];
        //$filmlist_back = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-go"]];
    } else if (in_array('filmlist-show', $action)) {
        $filmlist_name = "filmlist-show";
        $film = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist` WHERE `id` = '$param'")); 
        $filmlist_back = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-genre"]];
    } else if (in_array('filmlist-top', $action)) {
        $filmlist_name = "filmlist-top";
        $film = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_top` WHERE `id` = '$param'")); 
        $filmlist_back = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-top-list"]];
    }
    if ($film['photo']) {$film_page["photo"] = $film['photo'];}    
    $film_page["text"] .= "\n\n<b>".$film['title']."</b>\n";
    if ($film['genres']) {        
        $genres_array = preg_split("/,+/", $film['genres'], 0, PREG_SPLIT_NO_EMPTY);
        foreach($genres_array as $value) {
            $load_genre = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` WHERE `num` = '$value'")); 
            $load_array[] = $load_genre['name'];
        }
        $film_page["text"] .= "\n🎬 Жанр: ".implode(', ', $load_array);
    }
    if ($film['countries']) {
        $film['countries'] = preg_split("/,+/", $film['countries'], 0, PREG_SPLIT_NO_EMPTY);
        $film['countries'] = implode(', ', $film['countries']);
        $film_page["text"] .= "\n🌎 Страны: ".$film['countries'];
    }
    if ($film['film_length']) {
        $film_page["text"] .= "\n🕘 Продолжительность: ".filmlist_time($film['film_length']);
    }
    if ($film['description']) {
        $film_page["text"] .= "\n\n".$film['description'];
    }
    if ($film['link']) {
        $film_page["keyboard"][] = [["text" => "👀 Смотреть трейлер", "url" => $film['link']]];
    }/* 
    if (in_array('filmlist-random', $action)) {
        $film_page["keyboard"][] = [["text" => "🎲 Еще случайный фильм", "callback_data" => "filmlist_filmlist-random"]];
    } */
    if ($filmlist_back) {$film_page["keyboard"][] = $filmlist_back;}
    if (in_array('filmlist-random', $action)) {
        $arr['filmlist-random'] = $film_page;
    } else if (in_array('filmlist-show', $action)) {
        $arr['filmlist-show'] = $film_page;
    } else if (in_array('filmlist-top', $action)) {
        $arr['filmlist-top'] = $film_page;
    }
    $arr[$filmlist_name] = $film_page;  
}

if (in_array('filmlist-genre-list', $action)) {
    $arr['filmlist-genre-list']["text"] = "Выберите жанр фильма: ";
    $i = 0;
    $sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` ORDER BY `order` ASC");
    while($row = mysqli_fetch_assoc($sql) ){
        $ii = floor($i / 2);
        $arr['filmlist-genre-list']["keyboard"][$ii][] = ["text" => text($row['button'], 'bot'), "callback_data" => "filmlist_filmlist-genre_".$row['num']];
        $i++;
    }
    $arr['filmlist-genre-list']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "menu_menu"]];
    //$arr['filmlist-genre-list']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-go"]]; 
}


if (in_array('filmlist-genre', $action) OR in_array('filmlist-top-list', $action)) {  
    if (in_array('filmlist-genre', $action)) {
        $filmlist_name = "filmlist-genre";
        $filmlist_button = "filmlist-show";
        $table = "module_filmlist";
        $load_genre = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_genres` WHERE `num` = '$us[filmlist_1]'"));
        $list_page["text"] = "Список фильмов жанра: ".text($load_genre['button'], 'bot');
        $global_sql = " `genres` LIKE '%,$us[filmlist_1],%' AND `rating` > '".$set_filmlist['genre_rating']."' AND `rating_user_count` > '".$set_filmlist['genre_rating_user_count']."' ";
        $filmlist_back = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-genre-list"]];
    } else if (in_array('filmlist-top-list', $action)) {  
        $filmlist_name = "filmlist-top-list";      
        $list_page["text"] = "🏆 Список ТОП фильмов: ";
        $table = "module_filmlist_top";
        $filmlist_button = "filmlist-top";
        $global_sql = " `id` > '0' ";
        $us['filmlist_1'] = 1;
        $filmlist_back = [["text" => $dop_but['back'], "callback_data" => "menu_menu"]];
    }    
    if ($us['filmlist_2'] AND $us['filmlist_3']) {
        if ($us['filmlist_2'] == "next") {
            $dop_sql = " AND  `id` > '".$us['filmlist_3']."' ORDER BY `id` ASC ";
        } else if ($us['filmlist_2'] == "prew") {
            $dop_sql = " AND  `id` < '".$us['filmlist_3']."' ORDER BY `id` DESC ";
        }
    } else {
        $dop_sql = " ORDER BY `id` ASC ";
    }
    $sql = mysqli_query($CONNECT, "SELECT * FROM `$table` WHERE ".$global_sql." ".$dop_sql." LIMIT 10");
    while($row = mysqli_fetch_assoc($sql) ){
        $filmlist_list[] = $row;
    }
    if ($us['filmlist_2'] == "prew") {$filmlist_list = array_reverse($filmlist_list);}
    foreach($filmlist_list as $row) {
        if (!$num_prew) {$num_prew = $row["id"];} 
        $num_next = $row["id"];
        $list_page["keyboard"][] = [["text" => $row["title"], "callback_data" => "filmlist_".$filmlist_button."_".$row["id"]]];    
    }
    //кнопка предыдущие
    $count_prew = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `$table` WHERE ".$global_sql." AND `id` < '$num_prew'"))[0];
    if ($count_prew) {
        $button_genre[] = ["text" => "◀️ ПРЕДЫДУЩИЕ", "callback_data" => "filmlist_".$filmlist_name."_".$us['filmlist_1']."_prew_".$num_prew];
    }
    //кнопка следующие
    $count_next = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `$table` WHERE ".$global_sql." AND `id` > '$num_next'"))[0];
    if ($count_next) {
        $button_genre[] = ["text" => "СЛЕДУЮЩИЕ ▶️", "callback_data" => "filmlist_".$filmlist_name."_".$us['filmlist_1']."_next_".$num_next];
    }  
    if ($button_genre) {
        $list_page["keyboard"][] = $button_genre;
    }
    //$list_page["text"] .= "\n\n ".$count_prew." / ".$count_next;
    if ($filmlist_back) {$list_page["keyboard"][] = $filmlist_back;}
    $arr[$filmlist_name] = $list_page;  
    //дополнительное сообщение
    /* if (in_array('filmlist-top-list', $action)) { 
        array_unshift($action, "filmlist-top-list-image");// добавляем сообщение спереди в массив
        foreach($filmlist_list as $row) {
            $arr['filmlist-top-list-image']["media"][] = $row['photo'];
        }
    } */
}


if (in_array('filmlist-look', $action)) { 
    $arr['filmlist-look']["text"] = "🎥 Выберите фильм/сериал который хотите посмотреть:";
    $sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_look` ORDER BY `id` ASC LIMIT 90");
    while($row = mysqli_fetch_assoc($sql) ){
        $arr['filmlist-look']["keyboard"][] = [["text" => text($row['name'], 'bot'), "callback_data" => "filmlist_filmlist-look-message"]];
    }
    $arr['filmlist-look']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "menu_menu"]];
}

$arr['filmlist-look-message']["text"] = "🎥 Загружу завтра";
$arr['filmlist-look-message']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-look"]];

if (in_array('filmlist-look-list', $action)) {
    $arr['filmlist-look-list']["text"] = "🎥 Список фильмов:\n\n* Нажмите на кнопку названия фильма чтоб удалить его";
    $sql = mysqli_query($CONNECT, "SELECT * FROM `module_filmlist_look` ORDER BY `id` ASC LIMIT 90");
    while($row = mysqli_fetch_assoc($sql) ){
        $arr['filmlist-look-list']["keyboard"][] = [["text" => "❌ ".text($row['name'], 'bot'), "callback_data" => "filmlist_filmlist-look-delete_".$row['id']]];
    }
    $arr['filmlist-look-list']["keyboard"][] = [["text" => "➕ Добавить", "callback_data" => "filmlist_filmlist-look-add"]];
    $arr['filmlist-look-list']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];
}

$arr['filmlist-look-add']["text"] = "Введите название фильма";
$arr['filmlist-look-add']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "filmlist_filmlist-look-list"]];


$arr['filmlist-look-add-ok']["text"] = "✅ Фильм успешно добавлен";

?>