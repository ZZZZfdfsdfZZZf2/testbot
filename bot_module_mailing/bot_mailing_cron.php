<?php
$back_dir = explode('/', __DIR__);;
array_pop($back_dir);
$back_dir = implode('/', $back_dir);
include_once $back_dir.'/__config.php';
include_once $back_dir.'/bot_config.php';
include_once __DIR__.'/bot_mailing_config.php';
if (!file_exists(__DIR__.'/bot_mailing_cron.txt')) {file_put_contents(__DIR__.'/bot_mailing_cron.txt', 'ok');}


$date = date('H:i:s', strtotime('now'));
echo '</br> тип версии кода 04</br></br></br>';
echo 'Старт '.$date; 


if ($telegram_bot_token) {
    for ($i = 1; $i <= 9; $i++) {
        echo '</br>============================== проход '.$i;
        // ИЗ СОЗДАННЫХ => В ЗАГРУЗКА ЮЗЕРОВ
        $check_active = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `module_mailling` WHERE `status` = 'progress'"))[0]; 
        if (!$check_active) {//если есть хотя бы одна активная, остальные ждут в очереди в очереди
            $sql = mysqli_query($CONNECT, "SELECT * FROM `module_mailling` WHERE `status` = 'start' AND `date_wait` <= NOW() ORDER BY `id` ASC LIMIT 1");
            while($mes = mysqli_fetch_assoc($sql) ){
                $now_start = date('Y-m-d H:i:s', strtotime('now')); 
                $count_all = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user` "))[0];
                $count_admin = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_admin`"))[0];
                $count_all = $count_all - $count_admin + 1; //минус все работнкии и админы кроме одного админа кто запустил
                $user_id_max = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT MAX(`id`) FROM `bot_user` "))[0];
                mysqli_query($CONNECT, "UPDATE `module_mailling` SET `count_all` = '$count_all', `user_id_max` = '$user_id_max', `status` = 'progress', `date_start` = '$now_start' WHERE `id` = '$mes[id]'");    
                echo '</br>------------</br>Перевели рассылку  в активный режим № '.$mes['id'];
            }
        }
        sleep(5);
    }
}

?>