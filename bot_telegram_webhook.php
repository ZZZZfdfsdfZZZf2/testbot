<?php
include_once '__config.php';
include_once 'bot_config.php';

$webhook_file = 'bot_input.php'; //файл на который направляем webhook


echo '</br></br></br>'.$website.'/'.$webhook_file.'</br></br>';
if (!file_exists(__DIR__.'/'.$webhook_file)) {
    echo 'Error: NOT PAGE';
} else {
    if (mb_stripos($website, 'https://') === false) {
        echo 'Error: not HTTPS';
    } else {
        $setWebhook = telegram('setWebhook?url='.$website.'/'.$webhook_file);
        if ($setWebhook['ok'] == true AND $setWebhook['result'] == true) {
            $getWebhookInfo = telegram('getWebhookInfo');
            if ($getWebhookInfo['result']['url'] == $website.'/'.$webhook_file){
                $answer = telegram("sendMessage", ["chat_id" => '355590439', "text" => 'SUCCESSFULLY CONNECTED']);
                echo '</br>SUCCESSFULLY CONNECTED TO @'.$answer['result']['from']['username'];
                //telegram("deleteMessage", ["chat_id" => '355590439', "message_id" => $answer['result']['message_id']]);
            } else {
                echo 'ERROR: getWebhookInfo';
                echo '<pre>';
                print_r($getWebhookInfo);
                echo '</pre>';
                echo '</br></br>INFO: setWebhook';
                echo '<pre>';
                print_r($setWebhook);
                echo '</pre>';
            }
        } else {
            echo 'ERROR: setWebhook';
            echo '<pre>';
            print_r($setWebhook);
            echo '</pre>';
        } 
    }
}
?>