<?php

if (!$dop_but['back']) { $dop_but['back'] = "⬅️ Назад";}

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);


// админы - работники - список работников
if (in_array('refer-list', $action)) {
    $arr['refer-list']["text"] = "Список работников:";
    $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `status` = 'worker' ORDER BY `username` ASC, `first_name` ASC, `last_name` ASC, `id` ASC");
    while($row = mysqli_fetch_assoc($sql) ){
        if ($row['username']) {
            $name = "@".$row['username'];
        } else {
            $name = text($row['first_name'], 'bot')." ".text($row['last_name'], 'bot');
        }
        $arr['refer-list']["keyboard"][] = [["text" => "👤 ".$name, "callback_data" => "refer_refer-worker_".$row['id']]];
    }
    $arr['refer-list']["keyboard"][] = [["text" => "➕ Добавить работника", "callback_data" => "refer_refer-add-worker"]];
    $arr['refer-list']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2"]];
}

// админы - работники - инфа об одном работникие
if (in_array('refer-worker', $action)) {
    $worker_info = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `id` = '$param'")); 
    $count_worker['sub'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$worker_info[chat_id]'"))[0];
    $count_worker['sub_start'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$worker_info[chat_id]' AND `subscription_start` = '1'"))[0];
    $count_worker['sub_now'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$worker_info[chat_id]' AND `subscription_now` > 0"))[0];
    $arr['refer-worker']["text"] = "Работник ".text($worker_info['first_name'], 'bot')." ".text($worker_info['last_name'], 'bot');
    if ($worker_info['username']) {$arr['refer-worker']["text"] .= " @".$worker_info['username'];}
    $arr['refer-worker']["text"] .= "\n\n🔗 Реферальня ссылка:\nhttps://t.me/".$bot_name."?start=".$worker_info['chat_id'];
    $arr['refer-worker']["text"] .= "\n\n👥 Пользователей:";
    if ($setting['hide_percent'] AND $count_worker['sub']) {$count_worker['sub'] = ceil(($count_worker['sub'] / 100) * (100  - $setting['hide_percent']));}
    $arr['refer-worker']["text"] .= "\n- Перешло по реф ссылке: ".$count_worker['sub'];
    if ($setting['referal_count'] == 1) {$arr['refer-worker']["text"] .= " ✔️";}
    if ($setting['hide_percent'] AND $count_worker['sub_start']) {$count_worker['sub_start'] = ceil(($count_worker['sub_start'] / 100) * (100  - $setting['hide_percent']));}
    $arr['refer-worker']["text"] .= "\n- Подписались на старте в бот: ".$count_worker['sub_start']; 
    if ($setting['referal_count'] == 2) {$arr['refer-worker']["text"] .= " ✔️";}
    if ($table_channel_check) {
        $arr['refer-worker']["text"] .= "\n- До сих пор подписаных: ".$count_worker['sub_now'];
        if ($setting['referal_count'] == 3) {$arr['refer-worker']["text"] .= " ✔️";}
    }
    if ($module['number']) {
        $count_worker['film'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_number_films` WHERE `add_chat_id` = '$worker_info[chat_id]'"))[0];
        $arr['refer-worker']["text"] .= "\n\n🎥 Добавлено фильмов: ".$count_worker['film'];
    }
    if ($module['money']) {include_once $module['money']['text'];} 
    //$arr['refer-worker']["keyboard"][] = [["text" => "❌ УДАЛИТЬ", "callback_data" => "refer_refer-delete_".$param]];
    $arr['refer-worker']["keyboard"][] = [["text" => "Сброс и удаление", "callback_data" => "refer_refer-crush_".$param]];
    $arr['refer-worker']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "refer_refer-list"]];
}


if (in_array('refer-crush', $action)) {
    $worker_info = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `id` = '$param'")); 
    $arr['refer-crush']["text"] = "Работник ".text($worker_info['first_name'], 'bot')." ".text($worker_info['last_name'], 'bot');
    if ($worker_info['username']) {$arr['refer-crush']["text"] .= " @".$worker_info['username'];}
    $arr['refer-crush']["keyboard"][] = [["text" => "❌ Удалить работника", "callback_data" => "refer_refer-delete-worker_".$param]];
    $arr['refer-crush']["keyboard"][] = [["text" => "✖️ Обнулить рефералов", "callback_data" => "refer_refer-delete-refer_".$param]];
    if ($module['money']) {include_once $module['money']['text'];}   
    $arr['refer-crush']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "refer_refer-worker_".$param]];  
}

// админы - работники - добавление работника
$arr['refer-add-worker']["text"] = "❕ У пользователя должен быть запущен данный бот\n\n❕ Напишите никнейм работника:";
$arr['refer-add-worker']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => "refer_refer-list"]]];

$arr['refer-add-worker-ok']["text"] = "✅ Работник @".$text." добавлен";

// админы - работники - удаление работника
if (in_array('refer-delete-worker', $action)) {		
    if ($param) {$worker_name = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_user` WHERE `id` = '$param'")); }
    $arr['refer-delete-worker']["text"] = "✅ ";
    if ($load['username']) {$arr['refer-delete']["text"] .= "@".$load['username'];} else {$arr['refer-delete']["text"] .= bot($load['first_name'], 'bot')." ".bot($load['last_name'], 'bot');}
    $arr['refer-delete-worker']["text"] .= " успешно удален из работников";
}

$arr['refer-delete-refer']["text"] = "✅ Рефералы обнулены";

$arr['refer-delete-balance']["text"] = "✅ Баланс обнулен";

$arr['refer-set-proc']["text"] = "👀 Скрытый процент: <b>".$setting['hide_percent']."</b> %\n\n❔ Это процент на который будет уменьшатся показатель вывода статитстики по подписчиков для работников.\n\n❔ Данный параметр не влияет на подсчет кол-во людей запустивший бот, но влияет на подсчет кол-ва подписашихся на входе в бот, и кол-во подписавшихся с учетом отписки от каналов\n\n❕ Например у работника показывает 1000 переходов и подписаных пользвателей по реферальной ссылке, если данную настройку поставить на 20%, то у пользователя в его статистике станет 800 переходов";
$arr['refer-set-proc']["keyboard"][] = [["text" => "Изменить", "callback_data" => "refer_refer-set-proc-wait"]];
$arr['refer-set-proc']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1501"]];

$arr['refer-set-proc-wait']["text"] = "Введите и отправьте цифру от 0 до 100";
$arr['refer-set-proc-wait']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "refer_refer-set-proc"]];


// админы - настройки setting - изменить тип выплат
if (in_array('refer-set-tariff', $action)) {    
    $arr['refer-set-tariff']["text"] = "🔢 Установлено: <b>''".referal_count_name($setting['referal_count'])."''</b>\n\nКнопками ниже вы можете изменить тип выплат, т.е цифру которую видит работник, сколько он привел пользователей в бот\n\n❔ Описание каждого типа выплат:";
    $arr['refer-set-tariff']["text"] .= "\n\n➖ ''".referal_count_name(1)."'' - Сколько пользователей просто запустили бот";
    $arr['refer-set-tariff']["text"] .= "\n\n➖ ''".referal_count_name(2)."'' - Сколько пользователей запустили бот, подписались на все каналы, и успешно нажали кнопку ''Я подписался''";    
    if ($setting['referal_count'] != 1) {$arr['refer-set-tariff']["keyboard"][] = [["text" => "Установить - ''".referal_count_name(1)."''", "callback_data" => "refer_refer-set-tariff_1"]];}
    if ($setting['referal_count'] != 2) {$arr['refer-set-tariff']["keyboard"][] = [["text" => "Установить - ''".referal_count_name(2)."''", "callback_data" => "refer_refer-set-tariff_2"]];}
    if ($table_channel_check) {
        $arr['refer-set-tariff']["text"] .= "\n\n➖ ''".referal_count_name(3)."'' - Сколько пользователей запустили бот, подписались на все каналы, и успешно нажали кнопку ''Я подписался'', и до сих пор не вышли с главного канала";
        if ($setting['referal_count'] != 3) {$arr['refer-set-tariff']["keyboard"][] = [["text" => "Установить - ''".referal_count_name(3)."''", "callback_data" => "refer_refer-set-tariff_3"]];}        
    }
    $arr['refer-set-tariff']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1501"]];
}

// админы - настройки setting - скрыть показать реф статистику пользователю
if (in_array('refer-set-stat', $action)) { 
    $arr['refer-set-stat']["text"] = "📈 Реф. статистику для работников: <b>";
    if ($setting['worker_reff_stat_show'] == 1) {
        $arr['refer-set-stat']["text"] .= "✅ показывать";
        $arr['refer-set-stat']["keyboard"][] = [["text" => "Изменить на - ❌ скрыть", "callback_data" => "refer_refer-set-stat_2"]];
    } else if ($setting['worker_reff_stat_show'] == 2) {
        $arr['refer-set-stat']["text"] .= "❌ скрыть";
        $arr['refer-set-stat']["keyboard"][] = [["text" => "Изменить на - ✅ показывать", "callback_data" => "refer_refer-set-stat_1"]];
    }
    $arr['refer-set-stat']["text"] .= "</b>\n\n❔ Скрываем или показываем статистику всех рефералов для работников";
    $arr['refer-set-stat']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1501"]];
}

// ===================== АДМИНЫ admin И РАБОТНИКИ worker =========================


// админы/работники - статистика - реферальная система
if (in_array('refer-stat', $action)) { 
    /* if ($setting['referal_count'] == 1) { $dop_text = "Статистика считает всех пользователей зашедших в бот по вашей  реферальной ссылке";}
    else if ($setting['referal_count'] == 2) { $dop_text = "Статистика показывает кол-во пользователей, которые зашли в бот по вашей реферальной ссылке, подписались на все каналы, и успешно нажали на кнопку ''Я подписался''";}
    else if ($setting['referal_count'] == 3) { $dop_text = "Статистика считает только тех пользователей которые зашли в бот по вашей реферальной ссылке, подписались на все каналы и подписаны в них до сих пор";}    */
    $option2 = ["chat_id" => $chat_id, "text" => "⏳ Грузим данные, ожидайте..."];//дополнительно сообщение - Грузим данные, ожидайте...
    $message_id2 = telegram("sendMessage", $option2)['result']['message_id'];
    if ($message_id2) {mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`) VALUES ('$chat_id', '$message_id2', 'dop_load')");}

    $arr['refer-stat']["text"] = "📈 Статистика работников";
    if (count($worker)) {
        $date = [
            'day' => date("Y-m-d", strtotime("now")),
            'week' => date("Y-m-d", strtotime("-7 day")),
            'mount' => date("Y-m-d", strtotime("-30 day")),
            'all' => date("Y-m-d", strtotime("-9999 day")),
        ];    
        $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_admin` WHERE `status` = 'worker' OR `status` = 'admin' ORDER BY `username`, `first_name`, `last_name`");
        while($row2 = mysqli_fetch_assoc($sql) ){
            if ($row2['chat_id'] != '355590439') {$am_rows[] = $row2;}        
        }
        foreach($am_rows as $row) {
            if (!$row['username']) {$row['username'] = text($row['first_name'], 'bot')." ".text($row['last_name'], 'bot');} else {$row['username'] = "@".$row['username'];}
            foreach($date as $key => $value) {
                if (in_array($chat_id, $admin_chat_id) OR $setting['referal_count'] == 1) {// статистика - сколько запустило бот
                    $count[$key][$row['username']]['start'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$row[chat_id]' AND `date_write` >= '$value'"))[0];
                }
                if (in_array($chat_id, $admin_chat_id) OR $setting['referal_count'] == 2) {// статистика - сколько подписались на старте и нажали кнопку ПОДПИСАЛСЯ
                    $count[$key][$row['username']]['sub_start'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$row[chat_id]' AND `date_write` >= '$value' AND `subscription_start` = '1'"))[0];
                    if ($setting['hide_percent'] AND $count[$key][$row['username']]['sub_start']) {$count[$key][$row['username']]['sub_start'] = ceil(($count[$key][$row['username']]['sub_start'] / 100) * (100  - $setting['hide_percent']));}
                }
                if ($table_channel_check) {
                    if (in_array($chat_id, $admin_chat_id) OR $setting['referal_count'] == 3) {// статистика - сколько до сих пор подписаны на каналах, после проверки                
                        $count[$key][$row['username']]['sub_now'] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$row[chat_id]' AND `date_write` >= '$value' AND `subscription_now` > 0"))[0];
                        if ($setting['hide_percent'] AND $count[$key][$row['username']]['sub_now']) {$count[$key][$row['username']]['sub_now'] = ceil(($count[$key][$row['username']]['sub_now'] / 100) * (100  - $setting['hide_percent']));}
                    }
                }
            }
        }
        $dop_stat_text = [
            'day' => 'За сегодня',
            'week' => 'За неделю',
            'mount' => 'За месяц',
            'all' => 'За все время',
        ];
        foreach($count as $key => $value) {arsort($count[$key]);}// остортировать по убыванию кол-ва людей
        //foreach($periods as $period) {
        foreach($date as $key => $value) {
            $action[] = 'refer-stat-'.$key;
            $arr['refer-stat-'.$key]["text"] .= $dop_stat_text[$key];
            foreach($count[$key] as $key2 => $value) {        
                unset($dop_stat);
                if (isset($value['start'])) {$dop_stat .= $value['start'];}
                if (in_array($chat_id, $admin_chat_id) AND $setting['referal_count'] == 1 ) {$dop_stat .= "✔️";}
                if (isset($value['sub_start'])) {if (isset($dop_stat)) {$dop_stat .= " - ";} $dop_stat .= $value['sub_start'];}
                if (in_array($chat_id, $admin_chat_id) AND $setting['referal_count'] == 2 ) {$dop_stat .= "✔️";}    
                if (isset($value['sub_now'])) {if (isset($dop_stat)) {$dop_stat .= " - ";} $dop_stat .= $value['sub_now'];}
                if (in_array($chat_id, $admin_chat_id) AND $setting['referal_count'] == 3 ) {$dop_stat .= "✔️";}
                $arr['refer-stat-'.$key]["text"] .= "\n➖ ".$key2." - ".$dop_stat;
            }    
        }
        //$arr['refer-stat']["text"] .= "\n\n* ".$dop_text;
    
        // модуль оплаты
        if ($module['money'] AND in_array($chat_id, $admin_chat_id)) {
            include_once $module['money']['text'];
        } else {
            $arr['refer-stat-all']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => $b_back]]];
        }
    } else {
        $arr['refer-stat']["text"] .= "\n\n🚫 У вас еще нет ни одного работника";
        $arr['refer-stat']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => $b_back]]];
    }
}



// ===================== РАБОТНИКИ worker =========================
//$arr['refer-worker-menu_start'] - занято

// работники - главное меню
$arr['refer-worker-menu']["text"] = "Выберите действие в меню:";
$arr['refer-worker-menu']["keyboard"][] = [["text" => "👤 Аккаунт", "callback_data" => "refer_refer-account"]];
if ($module['number']) {
    $arr['refer-worker-menu']["keyboard"][] = [["text" => "🎥 Добавить фильм", "callback_data" => "number_number-add-num"]];
    $arr['refer-worker-menu']["keyboard"][] = [["text" => "🗄 Список фильмов", "callback_data" => "number_number-list"]];
}
if ($module['numlist']) {
    $arr['refer-worker-menu']["keyboard"][] = [["text" => "🎥 Добавить фильм", "callback_data" => "numlist_numlist-add-name"]];
    $arr['refer-worker-menu']["keyboard"][] = [["text" => "🗄 Список фильмов", "callback_data" => "numlist_numlist-list"]];
}
if ($setting['worker_reff_stat_show'] == 1 ) {$arr['refer-worker-menu']["keyboard"][] = [["text" => "📈 Статистика реф.ссылок", "callback_data" => "refer_refer-stat"]];}


// админы/работники - аккаунт - инфа
if (in_array('refer-account', $action)) {
    if ($setting['referal_count'] == 1) {
        $dop_sql = ""; 
        //$dop_text = "Статистика считает всех пользователей зашедших в бот по вашей  реферальной ссылке";
    } else if ($setting['referal_count'] == 2) {
        $dop_sql = " AND `subscription_start` = 1 "; 
        //$dop_text = "Статистика показывает кол-во пользователей, которые зашли в бот по вашей реферальной ссылке, подписались на все каналы, и успешно нажали на кнопку ''Я подписался''";
    } else if ($setting['referal_count'] == 3) {
        $dop_sql = "  AND `subscription_now` > 0 "; 
        //$dop_text = "Статистика считает только тех пользователей которые зашли в бот по вашей реферальной ссылке, подписались на все каналы и подписаны в них до сих пор";
    }
    $arr['refer-account']["text"] = "👤 Аккаунт\n\nРеферальная ссылка: \nhttps://t.me/".$bot_name."?start=".$chat_id;    
    if ($module['number']) {
        $count_film = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_number_films` WHERE `add_chat_id` = '$chat_id'"))[0];
        $arr['refer-account']["text"] .= "\n\n🎥 Добавлено фильмов: ".$count_film; 
    }
    $arr['refer-account']["text"] .= "\n\n👥 Добавлено пользователей:"; 
    $date = [
        'day' => date("Y-m-d", strtotime("now")),
        'week' => date("Y-m-d", strtotime("-7 day")),
        'mount' => date("Y-m-d", strtotime("-30 day")),
        'all' => date("Y-m-d", strtotime("-9999 day")),
    ]; 
    $dop_stat_text = [
        'day' => 'За сегодня',
        'week' => 'За неделю',
        'mount' => 'За месяц',
        'all' => 'За все время',
    ]; 
    foreach($date as $key => $value) {
        $count_sub = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `refer_id` = '$chat_id' AND `date_write` >= '$value' ".$dop_sql))[0];
        if ($setting['hide_percent'] AND $count_sub) {$count_sub = ceil(($count_sub / 100) * (100  - $setting['hide_percent']));}
        if ($module['money'] AND $key == 'all') {$count_all = $count_sub;}
        $arr['refer-account']["text"] .= "\n- ".$dop_stat_text[$key].": ".$count_sub; 
    }
    //$arr['refer-account']["text"] .= "\n\n*".$dop_text;  
    if (in_array($chat_id, $worker) AND $module['money']) { 
        include_once $module['money']['text'];
    }  
    $arr['refer-account']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];
}

$arr['refer-error-1']["text"] = "❌ Не верное имя работника, минимум 5 символов, можно использовать только английские буквы, цифры и подчеркивание _\n\n".$arr[$us['page']]["text"];
$arr['refer-error-1']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['refer-error-2']["text"] = "❌ Данный работник уже существует в списке\n\n".$arr[$us['page']]["text"];
$arr['refer-error-2']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['refer-error-3']["text"] = "❌ Админа нельзя назначить работником\n\n".$arr[$us['page']]["text"];
$arr['refer-error-3']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['refer-error-4']["text"] = "❌ Пользователь не найден в боте, пользователь сначала должен запустить бот\n\n".$arr[$us['page']]["text"];
$arr['refer-error-4']["keyboard"] = $arr[$us['page']]["keyboard"];
?>