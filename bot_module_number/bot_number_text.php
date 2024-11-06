<?php

if (!$dop_but['back']) { $dop_but['back'] = "⬅️ Назад";}

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

// модуль
if (!$arr['number-go']["text"]) {//может быть заменен через конфиг
    $arr['number-go']["text"] = "🔎 Для поиска отправьте КОД фильма/сериала";
}
if (!$module['menu']) {
    $arr['number-go']["menu"][] = ["🔎 ИСКАТЬ ФИЛЬМ"]; 
} else {
    $arr['number-go']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "menu_menu"]]; 
}
//if ($menu_back) {$arr['number-go']["menu"][] = [$menu_back];} // если есть модуль  меню menu то добавляем кнопку назад

if (in_array('number-film-name', $action)) {
    $arr['number-film-name']["text"] = "Фильм №".$text." называется:\n\n       <b>".text($kino['name'], 'bot')."</b>";   
    if ($kino['file_id']) {$arr['number-film-name'][$kino['file_type']] = $kino['file_id'];} 
}


// админы/работники - добавление фильма
$arr['number-add-num']["text"] = "Отправьте код фильма";
$arr['number-add-num']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => $b_back]]];

$arr['number-add-name']["text"] = "✔️ Код: ".$control['t_1']."\n\nОтправьте название фильма";
$arr['number-add-name']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => "number_number-add-num"]]];

$arr['number-add-file']["text"] = "✔️ Код: ".$control['t_1']."\n\n✔️ Название: ".text($control['t_2'], 'bot')."\n\nОтправьте фото или видео к фильму, либо нажмите пропутсить";
$arr['number-add-file']["keyboard"] = [[["text" => "➡️ ПРОПУСТИТЬ", "callback_data" => "number_number-add-save"]],[["text" => $dop_but['back'], "callback_data" => "number_number-add-name"]]];

// покажет сохраненный фильм
if (in_array('number-add-save', $action)) {
    $kino = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `num` = '$control[t_1]'")); 
    $arr['number-add-save']["text"] = "✅ Фильм сохранен\n\nКод: ".$kino['num']."\nНазвание: ".text($kino['name'], 'bot');
    $arr['number-add-save']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => $b_back]]];
    if ($kino['file_id']) {$arr['number-add-save'][$kino['file_type']] = $kino['file_id'];}
}

// админы/работники - перезапись фильма
if (in_array('number-add-rewrite', $action)) {
    $arr['number-add-rewrite']["text"] = "⚠️ Фильм с номером ".$text." уже существует под названием:\n\n        <b>".text($kino['name'], 'bot')."</b>";
    if (in_array($chat_id, $admin_chat_id) AND $chat_id != $kino['add_chat_id']) {
        $cust = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `chat_id` = '$kino[add_chat_id]'")); 
        $arr['number-add-rewrite']["text"] .= "\n\n⚠️ Добавил: ".text($cust['first_name'], 'bot')." ".text($cust['last_name'], 'bot');
        if ($cust['username']) {$arr['number-add-rewrite']["text"] .= " @".$cust['username'];}
    }
    $arr['number-add-rewrite']["keyboard"][] = [["text" => "➡️ Заменить название", "callback_data" => "number_number-add-name"]];
    $arr['number-add-rewrite']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "number_number-add-num"]];    
    if ($kino['file_id']) {$arr['number-add-rewrite'][$kino['file_type']] = $kino['file_id'];}
}
// список фильмов
if (in_array('number-list', $action)) {
    $arr['number-list']["text"] .= "🗄 Список фильмов:";
    if (in_array($chat_id, $admin_chat_id)) {            
        $count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_number_films`"))[0];
        if ($count) {            
            $m = 0;  
            for ($i = 0; $i <= 1; $i++) {
                if ($i == 0) {
                    $aray_chat_id = $admin_chat_id;
                    $name = "Админ";
                } else {
                    $aray_chat_id = $worker;
                    $name = "Работник";
                }
                foreach($aray_chat_id as $value) {
                    $cust = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `chat_id` = '$value'"));
                    $m++;
                    $mm = 'number-list-'.$m;
                    $action[] = $mm;
                    $arr[$mm]["text"] .= "👤 ".$name." - ";
                    if ($cust['username']) {$arr[$mm]["text"] .= "@".$cust['username'];}
                    $arr[$mm]["text"] .= " ".text($cust['first_name'], 'bot')." ".text($cust['last_name'], 'bot')."\n";
                    $f = 0;
                    $f_true = false;
                    $sql = mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `add_chat_id` = '$value' ORDER BY `num` ASC");//
                    while($row = mysqli_fetch_assoc($sql) ){
                        $f++;
                        if ($f > 50) {
                            $f = 0;
                            $m++;
                            $mm = 'number-list-'.$m;
                            $action[] = $mm;
                        }
                        $arr[$mm]["text"] .= "\n- ".$row['num']. " - ".text($row['name'], 'bot')." [<b>".$row['find_count']."</b>] /delete_".$row['num'];
                        if (strtotime('-2 week') > strtotime($row['date_request'])) {$arr[$mm]["text"] .= " 🔕";}
                        $f_true = true; 
                    }  
                    if (!$f_true) {
                        $array_key_last = array_key_last($action);
                        unset($action[$array_key_last]);
                    }
                }                 
            }
            $m++;
            $mm = 'number-list-'.$m;
            $action[] = $mm;
            $arr[$mm]["text"] .= "delete - Нажмите чтобы удалить фильм из списка\n🔕 - Фильм не запрашивали по номеру более 2-ух недель\n[<b>13</b>] - В скобках после названия фильма написано сколько запрашивали в поиске данный фильм за все время"; 
        } else {
            $arr['number-list']["text"] .= "\n\n🚫 У вас нет ни одного добавленного фильма";
        }
    } else {         
        $count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_number_films` WHERE `add_chat_id` = '$chat_id'"))[0];
        if ($count) {
            $arr['number-list']["text"] .= "\n";
            $f = 0;
            $mm = 'number-list-0';
            $action[] = $mm;
            $sql = mysqli_query($CONNECT, "SELECT * FROM `module_number_films` WHERE `add_chat_id` = '$chat_id' ORDER BY `num` ASC");
            while($line = mysqli_fetch_assoc($sql) ){
                $f++;
                if ($f > 50) {
                    $f = 0;
                    $m++;
                    $mm = 'number-list-'.$m;
                    $action[] = $mm;
                }
                $arr[$mm]["text"] .= "\n- ".$line['num']." - ".text($line['name'], 'bot');
                if (!$setting['number_count_film_worker_hide']) {$arr[$mm]["text"] .= " [<b>".$line['find_count']."</b>]";}                
                $arr[$mm]["text"] .= " /delete_".$line['num'];
                if (strtotime('-2 week') > strtotime($line['date_request'])) {$arr[$mm]["text"] .= " 🔕";}
            }
            $m++;
            $mm = 'number-list-'.$m;
            $action[] = $mm;
            $arr[$mm]["text"] .= "delete - Нажмите чтобы удалить фильм из списка\n🔕 - Фильм не запрашивали по номеру более 2-ух недель"; 
            if (!$setting['number_count_film_worker_hide']) {$arr[$mm]["text"] .= "\n[<b>13</b>] - В скобках после названия фильма написано сколько запрашивали в поиске данный фильм за все время"; }
        } else {
            $arr['number-list']["text"] .= "\n\n🚫 У вас нет ни одного добавленного фильма";
        }
    }    
    if (!$mm) {$mm = 'number-list';}
    $arr[$mm]["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => $b_back]]];
}

// админы - настройки setting - скрыть показать у работнков сколько цифр раз запросил тот или иной фильм
if (in_array('number-kinocount', $action)) {
    if (!isset($setting['number_count_film_worker_hide']))  {
        mysqli_query($CONNECT, "INSERT INTO `setting` (`name`, `param`) VALUES ('number_count_film_worker_hide', '0')");
        $setting['number_count_film_worker_hide'] = 0;
    }
    $arr['number-kinocount']["text"] = "🔢 Подсчет поиска фильмов у работников: <b>";
    if ($setting['number_count_film_worker_hide'] == 1) {
        $arr['number-kinocount']["text"] .= "❌ скрыть";
        $arr['number-kinocount']["keyboard"][] = [["text" => "Изменить на - ✅ показывать", "callback_data" => "number_number-kinocount_0"]];
    } else if ($setting['number_count_film_worker_hide'] == 0) {
        $arr['number-kinocount']["text"] .= "✅ показывать";
        $arr['number-kinocount']["keyboard"][] = [["text" => "Изменить на - ❌ скрыть", "callback_data" => "number_number-kinocount_1"]];
    }
    $arr['number-kinocount']["text"] .= "</b>\n\n❔ Скрываем или показываем работникам в списке загруженных фильмов статистику сколько запросили раз фильм";
    $arr['number-kinocount']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1501"]];
}

$arr['number-error-1']["text"] = "🚫 Код фильма не найден!";

$arr['number-error-2']["text"] = "🚫 Не верный ввод  кода, только цифры больше нуля!";

$arr['number-error-3']["text"] = "❌ Только файлы jpg или mp4\n\n".$arr[$us['page']]["text"];
$arr['number-error-3']["keyboard"] = $arr[$us['page']]["keyboard"];

$arr['number-error-4']["text"] = "❌ Вводить можно только цифры больше нуля\n\n".$arr[$us['page']]["text"]; // "/^[1-9]{1}[0-9]*$/"
$arr['number-error-4']["keyboard"] = $arr[$us['page']]["keyboard"];

$arr['number-error-5']["text"] = "❌ Вы используете запрещенные символы\n\n".$arr[$us['page']]["text"];
$arr['number-error-5']["keyboard"] = $arr[$us['page']]["keyboard"];


$arr['number-error-6']["text"] = "❌ Данный номер фильма уже занят другим работником\n\n".$arr[$us['page']]["text"];
$arr['number-error-6']["keyboard"] = $arr[$us['page']]["keyboard"];

?>