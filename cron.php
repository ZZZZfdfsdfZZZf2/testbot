<?php
include_once '__config.php';
include_once 'bot_config.php';



if ($table_channel_check){    

    $limit_second = 60;
    $micro_now = microtime(true);
    $stop_limit = $micro_now + $limit_second;






    for ($i = 1; $i <= 50; $i++) {
        $count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_user_subscription` WHERE  `subscription_now` = 1 "))[0];
        echo '</br>Еще нужно проверить'.$count;
        if ($count) {
            $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_user_subscription` WHERE `subscription_now` = 1 ORDER BY `id`  LIMIT 50");
            while($row = mysqli_fetch_assoc($sql) ){    
                mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = 2 WHERE `id` = '$row[id]'");
                $option = ["user_id" => $row['chat_id'], "chat_id" => $channel_check];
                $answer = telegram("getChatMember", $option);	
               /* echo '<pre>';
               print_r($answer);
               echo '</pre>'; */	
                if ($answer['result']['user'] AND $answer['result']['status'] != 'member') {
                    echo '</br>'.$row['chat_id']." - отписался ";
                    mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = 0 WHERE `id` = '$row[id]'");
                } else {
                    echo '</br>'.$row['chat_id']." - до сих пор подписан ";
                }
            } 
            $count--;
        } else {
            echo '</br>---------------------------------</br>';
            echo '</br>Время '.$now;
            $date_str_new = strtotime('now'); 
            $count_updates = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(1) FROM `bot_updates`"))[0];
            if ($count_updates) {
                $sql = mysqli_query($CONNECT, "SELECT * FROM `bot_updates`  ORDER BY `id` DESC LIMIT 1");
                while($row = mysqli_fetch_assoc($sql) ){
                    $date_str_old = $row['date_str'];
                }
                $sec = $date_str_new - $date_str_old;
                if ($sec >= 60) {
                    $minute = round($sec / 60);
                    if ($minute >= 60) {
                        $hour = round($minute / 60);
                        if ($hour >= 24) {
                            $day = round($hour / 24);
                            $time = $day." дней ".($hour - ($day * 24))." часов";
                        } else {
                            $time = $hour." часов ".($minute - ($hour * 60))." минут";
                        }
                    } else {
                        $time = $minute." минут ".($sec - ($minute * 60))." секунд";
                    }
                } else {
                    $time = $sec." секунд";
                }
                echo '</br>Заняло по длительности '.$time;
            } else {
                echo '</br>Время длительности не известно так как прогон первый';
                $time = 'менее 5 минут не показывает прогоны';
            }
                    
            if ($minute > 5 OR $hour OR $day OR !$count_updates) {
                mysqli_query($CONNECT, "INSERT INTO `bot_updates` (`date_write`, `date_str`, `time_text`) VALUES ('$now', '$date_str_new', '$time')");
            }
            echo '</br>---------------------------------</br>';
            mysqli_query($CONNECT, "UPDATE `bot_user_subscription` SET `subscription_now` = 1 WHERE `subscription_now` > 0 ");
        }
        sleep(1);  
    }
}

if (!file_exists(__DIR__.'/cron.txt')) {file_put_contents(__DIR__.'/cron.txt', 'ok');}
?>