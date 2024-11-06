<?php 
//нельзля использовать двойные кавычки в тексте - " , только одинакрные '. Нужны двойные кавычки, нужно поставить дважды одинарные, в боте они будут выглядеть как двойные
//перенос строки это \n После него лучше пробел не ставить, А писать вплотную, иначе получится пробел на новой строке. 

/* 
//Текстовое сообщение
$arr[0]["text"] = "Какойто_текст";

//Изображение
$arr[0]["text"] = "Какойто_текст";
$arr[0]["photo"] = "001.jpg";

//видео
$arr[0]["text"] = "Какойто_текст";
$arr[0]["video"] = "001.pm4"; //только mp4 и не более 50 МБ

//медиа
$arr[0]["text"] = "Какойто_текст";
$arr[0]["media"] = [
    $website."/bot_files/start.jpg",
    $website."/bot_files/clean.jpg",
];

//файлы до 20Mb
$arr[0]["text"] = "Какойто_текст";
$arr[0]["document"] = '/files/file.zip'; // файлы до 20 Mb

//файлы до 50Mb
$arr[0]["document"] = new CURLFile(realpath(__DIR__."/files/file.zip")); // файлы до 50 Mb // у этого способа нужно убрать в bot_telegram_send.php
$arr[0]["thumb"] = "attach://ico";//иконка файла
$arr[0]["ico"] = new CURLFile(realpath(__DIR__."/bot_files/ico_zip.jpg"));//иконка файла

//оповещение alert
$arr[0]["alert"] = "Очень_важное_оповещение"; //уведомление на экране 
//оповещение alert с кнокпкой
$arr[0]["alert"] = "Очень_важное_оповещение_с_кнопкой"; //уведомление на экране 
$arr[0]["alert_button"] = True; //кнопка OK

// Бот делает какое то действие
$arr[0]["act"] = "typing"; //печатает
$arr[0]["act"] = "upload_photo";
$arr[0]["act"] = "record_video";
$arr[0]["act"] = "upload_video";
$arr[0]["act"] = "record_voice";
$arr[0]["act"] = "upload_voice";
$arr[0]["act"] = "upload_document";
$arr[0]["act"] = "choose_sticker";
$arr[0]["act"] = "find_location";
$arr[0]["act"] = "record_video_note";
$arr[0]["act"] = "upload_video_note";

// пауза на определенное кол-во секунд, или выдача сообщения в определенное время
$arr[0]["timer"] = "+3 seconds"; // seconds minutes
$arr[0]["timer"] = "2022-01-01 00:00:00";

//стикер
$arr[0]["sticker"] = "CAACAgIAAxkBAAEB-BdgSNVLKcs8AlwVwcxfQ-LlRbkhvAACcgEAAladvQpNWrKil2eZsB4E";

//опрос с выдачей верного ответа
$arr[0]["question"] = "Текст_вопроса";
$arr[0]["question_anonymous"] = False; //анонимный опрос или нет
$arr[0]["question_answers"] = array("Ответ_0", "Ответ_1", "Ответ_2");
$arr[0]["question_right"] = 1; //Верный вариант ответа, не обязательный параметр

//ИГРА
$arr[0]["game_short_name"] = "soprano_verify"; // запустит игру, вписать нужно ее короткое имя

//карту отправить  - локацию гео
$arr[0]["latitude"] = '50.10';
$arr[0]["longitude"] = '20.452';

//опрос без верного ответа
$arr[0]["question"] = "Текст_вопроса_2";
$arr[0]["question_anonymous"] = True; //анонимный опрос или нет
$arr[0]["question_answers"] = array("Ответ_0", "Ответ_1", "Ответ_2");
$arr[0]['question_multiple_answers'] = True; //Несколько ответов - Функция возможна только если нет верного варианта ответа $arr[$value]["question_right"]

// --------------------- кнопки ---------------------

//кнопки  МЕНЮ
$arr[0]["menu"] = [
    ["Добавить аккаунт"],
    ["Сменить пароль в аккаунте"],
];

//кнопки на сообщениях
$arr[0]["keyboard"] = [
    [["text" => $dop_but['back'], "callback_data" => "action_2_1"]],
    [["text" => "Ссылка", "url" => "https://rutracker.org/forum/dl.php?t=6140789"]],
    [["text" => "Ссылка", "url" => "https://t.me/".$bot_name."?startgroup=true"]],//запустит выбор ГРУППЫ для отправки туда запуск бота
    [["text" => "выбор чата", "switch_inline_query" => "Отправляемый текст"]], //выбор в какой чат отправляем inline инфу
];

$arr[0]["menu"] = [[["text" => "ОТПРАВИТЬ ТЕЛЕФОН", "request_contact" => true]]];
$arr[0]["menu"] = [[["text" => "ОТПРАВИТЬ ЛОКАЦИЮ", "request_location" => true]]];

$arr[1]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2_1"],["text" => "➡️ Вперед", "callback_data" => "action_2_2"]];
$arr[1]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2"]];
$arr[1]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1"]];
$arr[1]["keyboard"][] = [["text" => $dop_but['close'], "callback_data" => "close"]];

if (in_array(3, $action)) {
    
}
*/

unset($arr);//не удалять, если идет повтор подгрузка страницы bot_text.php
$arr = $arr_plus;//загрузка дополнительных сообщений 

if (in_array('user-start', $action)) {
    $no_delete_bot = true;
    $no_delete_user = true;
}

$arr['status-ban']["text"] = "⚠️ Ваш аккаунт в бане";

$dop_but['forward'] = "➡️ Далее";
$dop_but['back'] = "⬅️ Назад";
$dop_but['close'] = "✖️ Закрыть";
$dop_but['home'] = "🏠 Домой";

if (in_array(2, $action) OR in_array('refer-worker-menu', $action)) {// сброс записок
    mysqli_query($CONNECT, "UPDATE `bot_user_admin` SET `t_1` = '', `t_2` = '', `t_3` = '', `t_4` = '', `t_5` = '' WHERE `user_id` = '$us[id]'");
}

// в общих сообщениях у админов и рабоников, кнопка назад ведет в разные меню
if (in_array($chat_id, $worker)) {$b_back = "refer_refer-worker-menu";}
else if (in_array($chat_id, $admin_chat_id)) {$b_back = "action_2";}

// =============== ЧЕМ ВЫШЕ МОДУЛЬ ТЕМ БОЛЬШЕ ОН В ПРИОРИТЕТЕ ===============


foreach($module as $key => $value) {
    // на какую страницу перекидывать в модуль после нажатия кнопки ПОДПИСАЛСЯ
    //file_put_contents(__DIR__.'/__00_.txt', json_encode($action, true));
    if (in_array(2401, $action) /* AND !$setting['start_message_hide'] */) {// при запуске 2401 сообщения, и если настроено что ОП при старте
        if ($key == 'menu') {
            $user_module_connect = "menu_menu_start";
        } else  if (in_array($key, ['welcome', 'mailing', 'money', 'refer'])) {// доп. модули которые не имею запуска после обязательной проверки

        } else if (!$user_module_connect) {
            $user_module_connect = $key."_".$key."-go_start";
        }
    }

    $action_one = preg_split("/[_|-]+/", $action[0], 0, PREG_SPLIT_NO_EMPTY);
    //file_put_contents(__DIR__.'/--1_'.$key.'.txt', $action_one[0]);
    if ($action_one[0] == $key) {
        //file_put_contents(__DIR__.'/--2_'.$key.'.txt', $text);
        include_once $module[$key]['text']; 
    }
}


// ======================= Стартовый модуль пустышка
if (!$user_module_connect) {$user_module_connect = "action_stop";}
$arr["stop"]["text"] = '❌ Модуль не подключен';


// ===================== АДМИН admin =========================

/* if (in_array('user-start', $action) AND $user_module_connect = 'rating_rating-go') { 
   file_put_contents(__DIR__.'/zzzzzzzzzzzzzzzzzzz.txt', $text);
    if ($us['rating_find_sex']){// чтоб повторно анкету не надо было заполнять
        $go = 'rating-menu';
        $param = '';
    }
}
 */

// админы - главное меню 
$arr[1]["text"] = "👤 Ваш статус администратор бота";
$arr[2]["text"] = "Выберите действие в меню:";
if ($module['clicker']) {$arr[2]["keyboard"][] = [["text" => "💎 Кликер", "callback_data" => "clicker_clicker-statistic"]];}
if ($module['rating']) {$arr[2]["keyboard"][] = [["text" => "👍👎 Статистика", "callback_data" => "rating_rating-statistic"], ["text" => "👍👎 ТОП🏆", "callback_data" => "rating_rating-top"]];}
if ($module['welcome']) {$arr[2]["keyboard"][] = [["text" => "👋 Авто приветствие", "callback_data" => "welcome_welcome-channel"]];}
if ($module['onemessage']) {$arr[2]["keyboard"][] = [["text" => "💭 Одно сообщение", "callback_data" => "onemessage_onemessage"]];}
if ($module['dialog']) {$arr[2]["keyboard"][] = [["text" => "💬 Статистика чата", "callback_data" => "dialog_dialog-statistic"]];}
if ($module['years']) {$arr[2]["keyboard"][] = [["text" => "💬 Статистика чата", "callback_data" => "years_years-statistic"]];}
if ($menu_param['action']) { 
    if (in_array('filmlist-look', $menu_param['action'])) { $arr[2]["keyboard"][] = [["text" => "Список  ''🎥 Загружу завтра''", "callback_data" => "filmlist_filmlist-look-list"]];}
}
if ($module['filmshow']) {
    $arr[2]["keyboard"][] = [["text" => "🎥 Загрузить видео", "callback_data" => "filmshow_filmshow-add-name"],["text" => "🗂 Список видео", "callback_data" => "filmshow_filmshow-list"]];
}
if ($module['filmshowsmart']) {
    $arr[2]["keyboard"][] = [["text" => "🎥 Загрузить видео", "callback_data" => "filmshowsmart_filmshowsmart-add-name"],["text" => "🗂 Список видео", "callback_data" => "filmshowsmart_filmshowsmart-list"]];
}
if ($module['number']) {$arr[2]["keyboard"][] = [["text" => "🎥 Добавить фильм", "callback_data" => "number_number-add-num"],["text" => "🗄 Список фильмов", "callback_data" => "number_number-list"]];}
if ($module['numberlink']) {$arr[2]["keyboard"][] = [["text" => "🎥 Добавить фильм", "callback_data" => "numberlink_numberlink-add-num"],["text" => "🗄 Список фильмов", "callback_data" => "numberlink_numberlink-list"]];}
if ($module['numlist']) {$arr[2]["keyboard"][] = [["text" => "🎬 Добавить фильм", "callback_data" => "numlist_numlist-add-name"],["text" => "🗄 Список фильмов", "callback_data" => "numlist_numlist-list"]];}

$arr[2]["keyboard"][] = [["text" => "📝 Обязательная подписка", "callback_data" => "action_1201"], ["text" => "📊 Статистика бота", "callback_data" => "action_1401"]];
if ($module['refer']) {
    $arr[2]["keyboard"][] = [["text" => "👥 Работники", "callback_data" => "refer_refer-list"],["text" => "📈 Статистика работников", "callback_data" => "refer_refer-stat"]];
}
if ($module['mailing']) {$arr[2]["keyboard"][] = [["text" => "💬 Создать рассылку", "callback_data" => "mailing_mailing-new"], ["text" => "📣 Активные рассылки", "callback_data" => "mailing_mailing-list"]];}
if ($module['refer']) {
    $arr[2]["keyboard"][] = [["text" => "⚙️ Настройки", "callback_data" => "action_1501"],["text" => "👤 Аккаунт", "callback_data" => "refer_refer-account"]];
} else {
    $arr[2]["keyboard"][] = [["text" => "⚙️ Настройки", "callback_data" => "action_1501"]];
}

// админы - каналы  - список
if (in_array(1201, $action)) {
    $arr[1201]["text"] = "Список каналов и ботов обязательной подписки в боте:";
    $i_1201 = 0;
    $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_channels` ORDER BY `orders`");
    while($row = mysqli_fetch_assoc($sql) ){
        unset($name_ch);
        if ($row['bot']) {//если бот
            if ($row['title']) {$name_ch = "🚫 🤖 ".text($row['title'], "bot");} else {$name_ch = "🚫 🤖 ".$row['bot'];}
        } else { // если канал
            if ($row['not_check']) {$name_ch = "🚫 ";}
            $name_ch .= "📣 ".text($row['title'], "bot");
        }   
        $arr[1201]["keyboard"][$i_1201][] = ["text" => $name_ch, "callback_data" => "action_1202_".$row['id']];
        if ($i_1201) {$arr[1201]["keyboard"][$i_1201][] = ["text" => "⬆️", "callback_data" => "action_1201_channel-up_".$row['id']];} else {$arr[1201]["keyboard"][$i_1201][] = ["text" => "➖", "callback_data" => "action_1201"];}
        $i_1201++;
    }
    $arr[1201]["keyboard"][] = [["text" => "➕ Добавить канал 📣", "callback_data" => "action_1231"]];
    $arr[1201]["keyboard"][] = [["text" => "➕ Добавить канал 📣 (без проверки)", "callback_data" => "action_1251"]];
    $arr[1201]["keyboard"][] = [["text" => "➕ Добавить бота 🤖 (без проверки)", "callback_data" => "action_1241"]];
    $arr[1201]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2"]];
}

// админы - каналы.боты - инфа об одном канале боте
if (in_array(1202, $action)) {   
    $ch_info = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `id` = '$param'"));
    if (($ch_info['channel_id'] != $channel_check AND $channel_check) OR !$channel_check) {$arr[1202]["keyboard"][] = [["text" => "❌ УДАЛИТЬ", "callback_data" => "action_1221_".$ch_info['id']]]; }
    if ($ch_info['bot']) {//если бот
        $arr[1202]["text"] = "🤖 Бот\n";
        $arr[1202]["text"] .= "\nБот: ".$ch_info['bot'];
        if ($ch_info['title']) {$arr[1202]["text"] .= "\nНазвание: ".text($ch_info['title'], 'bot');}
        if (mb_stripos($ch_info["channel_link"], '?') !== false) {$arr[1202]["text"] .= "\nСсылка: ".$ch_info["channel_link"];}
    } else {
        $arr[1202]["text"] = "📣 Канал\n";  
        if ($ch_info['title']) {$arr[1202]["text"] .= "\nНазвание: ".text($ch_info['title'], 'bot');}
        if ($ch_info['channel_name']) {$arr[1202]["text"] .= "\nСсылка: ".$ch_info['channel_name'];}
        if (preg_match("/^https:\/\/t.me\/\+(.){5,}$/", $ch_info['channel_link'])) {
            $arr[1202]["text"] .= "\nИнвайт ".$ch_info['channel_link'];
            $arr[1202]["keyboard"][] = [["text" => "📝 Редактировать инвайт", "callback_data" => "action_1211_".$param]];
        }	
    }  		
    $arr[1202]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1201"]];       
}

// админы - каналы - редактирование канала
$arr[1211]["text"] = "📝 Введите новый инвайт";
$arr[1211]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1202_".$control['t_1']]];
$arr[1212]["text"] = "✔️ Инвайт сохранен";
$arr[1212]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1202_".$control['t_1']]];

// админы - каналы - канал удален
if (in_array(1221, $action)) { 
    $arr[1221]["text"] = "✅ Канал  успешно удален";
}

// админы - каналы - добавление канала
$arr[1231]["text"] = "1️⃣ Добавьте данного бота на канал, с разрешением ''добавлять участников''\n\n2️⃣ Напишите имя канала (@name) или инвайт:";
$arr[1231]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1201"]];

$arr[1232]["text"] = "✅ Канал добавлен"; 


$arr[1233]["text"] = "Перешлите любой пост с канала\n\n❔ Если вы не можете сделать репост, напишите сюда id канала (узнать id канала можете тут: @username_to_id_bot отправив ему инвайт)";
$arr[1233]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1201"]];


$arr[1241]["text"] = "Напишите ссылку бота\n\n❔ Разрешены ссылки типа: \n@name_bot \nhttps://t.me/name_bot \nhttps://t.me/name_bot?start=xxx"; 
$arr[1241]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1201"]];

$arr[1242]["text"] = "✔️ ".$text."\n\nНапишите название бота которое будет на кнопке у обязательной подписке или нажмите ''пропустить'' чтоб на кнопке оставить ссылку на бот - ".$bot_name;
$arr[1242]["keyboard"][] = [["text" => "Пропустить", "callback_data" => "action_1243"]];
$arr[1242]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1241"]];

$arr[1243]["text"] = "✅ Канал добавлен"; 

$arr[1251]["text"] = "Пришлите инвайт на канал, если канал без инвайта, нажмите БЕЗ ИНВАЙТА";
$arr[1251]["keyboard"][] = [["text" => "БЕЗ ИНВАЙТА", "callback_data" => "action_1252"]];
$arr[1251]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1201"]];

$arr[1252]["text"] = "Перешлите любой пост с канала";
$arr[1252]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1201"]];

// админы - общая статистика бота
$arr[1401]["text"] = "📊 Статистика бота\n";
if (in_array(1401, $action)) {
    //дополнительно сообщение - Грузим данные, ожидайте...
    $option2 = ["chat_id" => $chat_id, "text" => "⏳ Грузим данные, ожидайте..."];
    $message_id2 = telegram("sendMessage", $option2)['result']['message_id'];
    if ($message_id2) {mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`) VALUES ('$chat_id', '$message_id2', 'dop_load')");}
    $count_1401_subscription = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription`"))[0];
    $arr[1401]["text"] .= "\nПользователей в боте:\n➖ Всего:  ".money($count_1401_subscription);
    $count_1401_start = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `subscription_start` = '1'"))[0];
    $arr[1401]["text"] .= "\n➖ Подписались на старте:  ".money($count_1401_start);
    $count_1401_live = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user`"))[0];
    $arr[1401]["text"] .= "\n➖ Живых:  ".money($count_1401_live);
    $arr[1401]["text"] .= "\n➖ Заблокированых:  ".money(($count_1401_subscription - $count_1401_live));

    $arr[1401]["text"] .= "\n\nЗашло в бот:";
    for ($i = 0; $i <= 30; $i++) {
        if ($i) {$date_1401 = '-'.$i.' day';} else {$date_1401 = 'now';}        
        $date_min = date('Y-m-d', strtotime($date_1401)); 
        $count_data['date'][$i] = date('j', strtotime($date_1401)); 
        $count_data['count_1'][$i] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `date_write` = '$date_min'"))[0];
        $count_data['count_2'][$i] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `subscription_start` = 1 AND `date_write` = '$date_min'"))[0];        
        if ($table_channel_check) { 
            $count_data['count_3'][$i] = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `subscription_now` > 0 AND `date_write` = '$date_min'"))[0];        
        }
    }   
    $arr[1401]["text"] .= "\n➖ Сегодня:  ".money($count_data['count_1'][0]);
    $arr[1401]["text"] .= "\n➖ Вчера:  ".money($count_data['count_1'][1]);
    $date_week = date('Y-m-d', strtotime('-1 week')); 
    $count_data_week = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `date_write` >= '$date_week'"))[0];
    $arr[1401]["text"] .= "\n➖ За неделю:  ".money($count_data_week);
    $date_month = date('Y-m-d', strtotime('-1 month')); 
    $count_data_month = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `date_write` >= '$date_month'"))[0];
    $arr[1401]["text"] .= "\n➖ За месяц:  ".money($count_data_month);
     
    //график
    if (file_exists(__DIR__.'/graph.php')) {
        include_once "graph.php";
        if ($graph_image) {$arr[1401]["photo"] = $website."/".$graph_image;}
    }
    if ($table_channel_check) {
        $count_1401 = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `subscription_now` > 0"))[0];
        $arr[1401]["text"] .= "\n\nПользоватей на главном канале:\n➖ Подписаных:  ".money($count_1401);
        $count_1401_m = $count_1401_subscription - $count_1401;
        $arr[1401]["text"] .= "\n➖ Отписаных: ".money($count_1401_m);
    }
    
    if ($module['refer']) {
        if (count($worker)) {$count_1401 = count($worker);} else {$count_1401 = 0;}
        $arr[1401]["text"] .= "\n\nРаботников:\n➖ Всего:  ".money($count_1401);  
    }
    if ($table_channel_check AND $chat_id == '355590439') {
        $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_updates` ORDER BY `id` DESC LIMIT 1");
        while($row = mysqli_fetch_assoc($sql) ){
            $updates = $row;
        }  
        $up_count_all = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `subscription_now` > 0"))[0];
        $up_count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE `subscription_now` = 2"))[0];
        if ($up_count_all != 0) {$up_count_c = round(100 * $up_count / $up_count_all);}
        if ($updates) {
            $arr[1401]["text"] .= "\n\n➖➖➖➖➖➖➖➖\n🔄 Последний проход № ".$updates['id']."\nЗакончен: ".preg_replace("/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/", '$4:$5 $3.$2.$1', $updates['date_write']);
            if ($updates['time_text']) {$arr[1401]["text"] .= "\nДлительность: ".$updates['time_text'];}            
        } else {
            $arr[1401]["text"] .= "\n\n➖➖➖➖➖➖➖➖\nЕще не было первого прохода";
        }
        $arr[1401]["text"] .= "\n\n🔄 Нынешний круг: ".$up_count_all." / ".$up_count." - ".$up_count_c." %";
    }
    $arr[1401]["keyboard"][] = [["text" => "📝 Файл для True Checker", "callback_data" => "action_1402"]];
    $arr[1401]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2"]];
}
/* 
// создание файла для True Checker
if (in_array(1402, $action)) { 
    //дополнительно сообщение - Грузим данные, ожидайте...
    $option2 = ["chat_id" => $chat_id, "text" => "⏳ Грузим данные, ожидайте..."];
    $message_id2 = telegram("sendMessage", $option2)['result']['message_id'];
    if ($message_id2) {mysqli_query($CONNECT, "INSERT INTO `bot_user_history` (`chat_id`, `message_id`, `types`) VALUES ('$chat_id', '$message_id2', 'dop_load')");}

    $folder = '/bot_load/TrueChecker';
    if (!is_dir(__DIR__.$folder)) {mkdir(__DIR__.$folder, 0777, true);}
    $files = scandir(__DIR__.$folder);
    $files = array_slice($files, 2);
    foreach($files as $file) {unlink(__DIR__.$folder.'/'.$file);}
    $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user`");
    while($row = mysqli_fetch_assoc($sql) ){
        $user_list .= $row['chat_id']."\n";
    }
    file_put_contents(__DIR__.$folder.'/users.txt', $user_list);// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!  НЕ УДАЛЯТЬ файла для True Checker !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    $arr[1402]["text"] = "Файл для проверки в боте True Checker @TrueCheckerBot ";
    //$arr[1402]["document"] = $website.$folder.'/users.txt';// txt  тка нельзя
    $arr[1402]["document"] = new CURLFile(realpath(__DIR__.$folder.'/users.txt'));
    $arr[1402]["document"] = $folder.'/users.txt';
    $arr[1402]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2"]];
} */

// админы - настройки setting
$arr[1501]["text"] = "Настройки бота:";
if ($module['refer']) {
    $arr[1501]["keyboard"][] = [["text" => "👀 Скрытый процент", "callback_data" => "refer_refer-set-proc"]];
    $arr[1501]["keyboard"][] = [["text" => "🔢 Выбор выплат", "callback_data" => "refer_refer-set-tariff"]];
    $arr[1501]["keyboard"][] = [["text" => "📈 Реф. статистика", "callback_data" => "refer_refer-set-stat"]];
}
//$arr[1501]["keyboard"][] = [["text" => "📝 Обязательная подписка на старте", "callback_data" => "action_1541"]];
$arr[1501]["keyboard"][] = [["text" => "🔤 Тип кнопок обязательной подписки", "callback_data" => "action_1531"]];
if ($module['money']) {
    $arr[1501]["keyboard"][] = [["text" => "💲 Ценник за юзера", "callback_data" => "money_money-price-all"]];
    $arr[1501]["keyboard"][] = [["text" => "💲 Минимальная сумма вывода", "callback_data" => "money_money-out-min"]];
}
if ($module['dialog']) {
    $arr[1501]["keyboard"][] = [["text" => "⭐️ Настройки ПРЕМИУМа", "callback_data" => "dialog_dialog-setting"]];
}
if ($module['rating']) {
    $arr[1501]["keyboard"][] = [["text" => "👑 Настройки VIP", "callback_data" => "rating_rating-setting"]];
}
if ($module['number']) {
    $arr[1501]["keyboard"][] = [["text" => "🔢 Подсчет поиска фильмов", "callback_data" => "number_number-kinocount"]];
}
$arr[1501]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_2"]];


// админы - настройки setting - изменить названия на стартовых кнопках
if (in_array(1531, $action)) { 
    $arr[1531]["text"] = "Надпись на кнопках обязательной подписки:";
    $dop_1531[$setting['button_name']] = "✅ ";
    $arr[1531]["keyboard"][] = [["text" => $dop_1531[1]."Название каналов и ботов", "callback_data" => "action_1531_1"]];
    $arr[1531]["keyboard"][] = [["text" => $dop_1531[2]."Нумерацию - КАНАЛ 1 / БОТ 1", "callback_data" => "action_1531_2"]];
    $arr[1531]["keyboard"][] = [["text" => $dop_1531[3]."Нумерацию - канал 1 / бот 2", "callback_data" => "action_1531_3"]];
    $arr[1531]["keyboard"][] = [["text" => $dop_1531[4]."Надпись - ➕ подписаться", "callback_data" => "action_1531_4"]];
    $arr[1531]["keyboard"][] = [["text" => $dop_1531[5]."Надпись - ➕ ПОДПИСАТЬСЯ", "callback_data" => "action_1531_5"]];
    $arr[1531]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1501"]];
}

if (in_array(1541, $action)) { 
    $arr[1541]["text"] = "Обязательная подписка на старте: ";
    if ($setting['start_message_hide'] == 1) {
        $arr[1541]["text"] .= "❌ скрыть";
        $arr[1541]["keyboard"][] = [["text" => "Изменить на - показывать", "callback_data" => "action_1541_0"]];
    } else {
        $arr[1541]["text"] .= "✅ показывать";
        $arr[1541]["keyboard"][] = [["text" => "Изменить на - скрыть", "callback_data" => "action_1541_1"]];
    }    
    $arr[1541]["text"] .= "\n\n❔ При старте бота юзером, при включеном параметре, сначала просит подписаться на обязательные каналы, при выключенном сразу присылает меню бота, и при определенных действиях запрашивает подписку на обязательные каналы";
    $arr[1541]["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "action_1501"]];
}

// ===================== ОБЫЧНЫЕ ПОЛЬЗОВАТЕЛИ other =========================


// обязательная подписка
if (in_array(2401, $action)) { 
    if (!$arr[2401]["text"]) {$arr[2401]["text"] = "📝 <b>Для использования бота, вы должны быть подписаны на наши каналы:</b>";}    
    $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_channels` WHERE `channel_link` != '' AND (`channel_link` != '' OR `channel_name` != '') ORDER BY `orders`");
    while($row = mysqli_fetch_assoc($sql) ){
        if ($channels_error) {//какие то каналы не прошли с первого раза и показываем куда не вошли
            if ($row['bot']) {
                $smile_2401 = "✅    "; 
            } else {
                if (in_array($row['channel_name'], $channels_error) OR in_array($row['channel_id'], $channels_error)) {$smile_2401 = "❌    ";} else {$smile_2401 = "✅    ";}
            }            
        } else {
            $smile_2401 = "";
        }
        if ($setting['button_name'] == 1){
            if ($row['bot']) {
                if ($row['title']) {$b_name = text($row['title'], 'bot');} else {$b_name = $row['bot'];}
            } else {
                if ($row['title']) {$b_name = text($row['title'], 'bot');} else {$b_name = "➕ ПОДПИСАТЬСЯ";}
            }
        } else if ($setting['button_name'] == 2) {
            $i++;
            if ($row['bot']) {
                $b_name = "🤖 БОТ ".$i;
            } else {
                $b_name = "📣 КАНАЛ ".$i;
            }
        } else if ($setting['button_name'] == 3) {
            $i++;
            if ($row['bot']) {
                $b_name = "🤖 бот ".$i;
            } else {
                $b_name = "📣 канал ".$i;
            }
        } else if ($setting['button_name'] == 4) {
            $b_name = "➕ подписаться";
        } else if ($setting['button_name'] == 5) {
            $b_name = "➕ ПОДПИСАТЬСЯ";
        }
        $arr[2401]["keyboard"][] = [["text" => $smile_2401.$b_name, "url" => $row['channel_link']]];
    }
    if (!$arr_subscribed) {$arr_subscribed = '✔️ Я ПОДПИСАЛСЯ ✔️';}//🥤 Я ПОДПИСАЛСЯ 🥤

    $arr[2401]["keyboard"][] = [["text" => $arr_subscribed, "callback_data" => $user_module_connect]];  
}
$arr[2402]["alert"] = "Вы должны подписаться на все каналы"; //уведомление на экране 
$arr[2402]["alert_button"] = True; //кнопка OK

// ======================= ОШИБКИ =====================
$arr['error-number-1']["text"] = "❌ Вводить можно только цифры\n\n".$arr[$us['page']]["text"]; // "/^[0-9]+$/"
$arr['error-number-1']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-number-3']["text"] = "❌ Вводить можно только цифры и точку для дробных чисел\n\n".$arr[$us['page']]["text"];
$arr['error-number-3']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-number-4']["text"] = "❌ Не верный ввод, только число от 0 до 100\n\n".$arr[$us['page']]["text"]; 
$arr['error-number-4']["keyboard"] = $arr[$us['page']]["keyboard"];




$arr['error-link-1']["text"] = "❌ Не верный ввод ссылки, разрешено:\n- Коротка ссылка: @channel\n- Обычная ссылка: https://t.me/channel\n- Инвайт: https://t.me/+xxxxxxxxxx\n\n".$arr[$us['page']]["text"];
$arr['error-link-1']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-link-2']["text"] = "❌ Данный ссылка уже существует в списке\n\n".$arr[$us['page']]["text"];
$arr['error-link-2']["keyboard"] = $arr[$us['page']]["keyboard"];

$arr['error-invite-1']["text"] = "❌ Не верный инвайт, должен начинатся с https://t.me/+\n\n".$arr[$us['page']]["text"];
$arr['error-invite-1']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-invite-2']["text"] = "❌ Данный инвайт уже существует в списке\n\n".$arr[$us['page']]["text"];
$arr['error-invite-2']["keyboard"] = $arr[$us['page']]["keyboard"];

$arr['error-channel-1']["text"] = "❌ Не верное id канала, должно начинатся со знака минус и быть длинее шести символов\n\n".$arr[$us['page']]["text"];
$arr['error-channel-1']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-channel-2']["text"] = "❌ Данный ID канала уже существует\n\n".$arr[$us['page']]["text"];
$arr['error-channel-2']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-channel-3']["text"] = "❌ Репост сообщения должен быть из канала\n\n".$arr[$us['page']]["text"];
$arr['error-channel-3']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-channel-4']["text"] = "❌ Канал не был добавлен, так как данный бот отстутствует на канале или боте не дали права доступа к ''Добавлению участников''";
$arr['error-channel-5']["text"] = "❌ Ссылка на бот не верна\n\n".$arr[$us['page']]["text"];
$arr['error-channel-5']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-channel-6']["text"] = "❌ Данный бот уже существует\n\n".$arr[$us['page']]["text"];
$arr['error-channel-6']["keyboard"] = $arr[$us['page']]["keyboard"];
$arr['error-channel-7']["text"] = "❌ Не возможно добавить данный канал без инвайта, так как у него нет собственной ссылки\n\n".$arr[$us['page']]["text"];
$arr['error-channel-7']["keyboard"] = $arr[$us['page']]["keyboard"];

$arr['error-film-name-1']["text"] = "🚫 Фильм не найден!";

$arr['error-channel-delete']["text"] = "❌ Основной канал нельзя удалить";



?>