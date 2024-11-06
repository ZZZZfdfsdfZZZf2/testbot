<?php

if (!$dop_but['back']) { $dop_but['back'] = "⬅️ Назад";}

$back_dir = explode('/', __DIR__);
array_pop($back_dir);
$back_dir = implode('/', $back_dir);

// модуль

if (in_array('welcome-channel', $action)) {
    $arr['welcome-channel']["text"] = "Каналы на которых будет срабатывать авто приветствие:";
    $sql = mysqli_query($CONNECT, "SELECT * FROM `module_welcome_channel` ");
    while($row = mysqli_fetch_assoc($sql) ){
        if ($row['title']) {
            $name_ch = text($row['title'], 'bot');
        } else if ($row['channel_name']) {
            $name_ch = $row['channel_name'];
        } else if ($row['channel_id']) {
            $name_ch = $row['channel_id'];
        } else {
            $name_ch = "ошибка";
        }    
        $arr['welcome-channel']["keyboard"][] = [["text" => $name_ch." ➖ ".money($row['count']), "callback_data" => "welcome_welcome-channel-one_".$row['id']]];
    }    
    $arr['welcome-channel']["keyboard"][] = [["text" => "➕ Добавить канал", "callback_data" => "welcome_welcome-channel-add"]];
    if (!$setting['welcome_timer']) {$setting['welcome_timer'] = 0;}    
    $arr['welcome-channel']["keyboard"][] = [["text" => "⏳ Таймер: ".$setting['welcome_timer']." ".words($setting['welcome_timer'], ['секунда', 'секунды', 'секунд']), "callback_data" => "welcome_welcome-channel-timer"]];
    $arr['welcome-channel']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => $b_back]];
}

if (in_array('welcome-channel-timer', $action) OR in_array('welcome-error-4', $action)) {
    if (!$setting['welcome_timer']) {$setting['welcome_timer'] = 0;}    
    $arr['welcome-channel-timer']["text"] = "⏳ Таймер: <b>".$setting['welcome_timer']." ".words($setting['welcome_timer'], ['секунда', 'секунды', 'секунд'])."</b>";
    if ($setting['welcome_timer'] > 60) {
        $welcome_minute = floor($setting['welcome_timer'] / 60);
        $welcome_sec = $setting['welcome_timer'] - ($welcome_minute * 60);
        $arr['welcome-channel-timer']["text"] .=  " <i>(".$welcome_minute." ".words($welcome_minute, ['минута', 'минуты', 'минут'])." ".$welcome_sec." ".words($welcome_sec, ['секунда', 'секунды', 'секунд']).")</i>";
    }
    $arr['welcome-channel-timer']["text"] .= "\n\nЧтобы изменить параметр, отправьте цифру от 0 до 290";
    $arr['welcome-channel-timer']["text"] .= "\n\n❔ Через сколько секунд запуститься бот, после добавлеиия пользователя на канал";
    $arr['welcome-channel-timer']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "welcome_welcome-channel"]];
} 

// админы - каналы - инфа об одном канале
if (in_array('welcome-channel-one', $action)) {   
    $ch_info = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `module_welcome_channel` WHERE `id` = '$param'"));
    $arr['welcome-channel-one']["text"] = "📣 Канал\n";
    if ($ch_info['title']) {$arr['welcome-channel-one']["text"] .= "\nНазвание: ".text($ch_info['title'], 'bot');}
    if ($ch_info['channel_name']) {$arr['welcome-channel-one']["text"] .= "\nСсылка: ".$ch_info['channel_name'];}
    $arr['welcome-channel-one']["text"] .= "\n\nКол-во переходов с канала в бот: ".money($ch_info['count']);
    $arr['welcome-channel-one']["keyboard"][] = [["text" => "❌ УДАЛИТЬ", "callback_data" => "welcome_welcome-channel-delete_".$ch_info['id']]];
    $arr['welcome-channel-one']["keyboard"][] = [["text" => $dop_but['back'], "callback_data" => "welcome_welcome-channel"]];       
}


$arr['welcome-channel-delete']["text"] = "✅ Канал успешно удален";


$arr['welcome-channel-add']["text"] = "1️⃣ Добавьте данного бота на канал, с разрешением ''добавлять участников''\n\n2️⃣ Отправьте сюда любой пост с канала";
$arr['welcome-channel-add']["keyboard"] = [[["text" => $dop_but['back'], "callback_data" => "welcome_welcome-channel"]]];

$arr['welcome-channel-save']["text"] = "✅ Канал добавлен"; 

$arr['welcome-error-1']["text"] = "❌ Должен быть репост сообщения только из канала\n\n".$arr[$us['page']]["text"];
$arr['welcome-error-1']["keyboard"] = $arr[$us['page']]["keyboard"];

$arr['welcome-error-2']["text"] = "❌ Канал не был добавлен, так как данный бот отстутствует на канале или боте не дали права доступа к ''Добавлению участников''";

$arr['welcome-error-3']["text"] = "❌ Данный ID канала уже существует\n\n".$arr[$us['page']]["text"];
$arr['welcome-error-3']["keyboard"] = $arr[$us['page']]["keyboard"];


$arr['welcome-error-4']["text"] = "❌ Должна быть цифра от 0 до 290\n\n".$arr[$us['page']]["text"];
$arr['welcome-error-4']["keyboard"] = $arr[$us['page']]["keyboard"];

?>