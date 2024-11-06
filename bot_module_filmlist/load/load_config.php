<?php

function load_filmlist($type, $get = '') {
    $url = 'https://kinopoiskapiunofficial.tech';
    //$key = 'aa29058f-cffc-4bc3-8ffd-b33fb04cf8fd';
    //$key = '3a9e4b68-3b45-4d16-9595-e3482438e9c0';
    //$key = 'cf0ba0d4-582f-4593-82e5-4095823ce9df';
    //$key = 'b14c5f51-331f-4aad-9827-eff0a0b475f2';
    //$key = '0724d1ea-60d4-42c6-9b5a-31771e5d7aa3';
    $key = '12965901-83f5-4a7b-8b55-7c45d07e5409';
    //$key = '7a805a43-7f6b-4ba0-bdd8-dd023ca4ab05';
    //$key = 'f18e5964-f3e8-46b8-8f63-abf63a4671c9';
    //$key = '42930711-9912-495c-8d15-1978beeba74b';
    //$key = '16778e5c-5659-4344-8d8e-ab82f1a37c66';
    //$key = 'bff33646-ad18-498c-a399-842f17b1303a';
    //$key = '84ec02a6-9ea5-4922-8d7c-5bbee0eb84ad';
    //$key = '4876090b-f03e-4e8c-887b-ad989a70df3d';
    //$key = 'e12c3be5-9f32-4de1-99aa-0f58ecdc7fb9';
    //$key = '6b0abf68-d61a-4aa0-b1e9-9b211d3b7dd3';
    //$key = '89bb6dbb-7ad9-4f68-bb75-06a028a42bb1';
    if ($get) {$get = "?".http_build_query($get);}
    $arr = [
        CURLOPT_URL => $url.$type.$get ,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 20, 
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_MAXREDIRS => 10, 
        CURLOPT_FAILONERROR => true, 
        CURLOPT_HEADER => 0,
        CURLOPT_ENCODING => "",
        CURLOPT_AUTOREFERER => true,
        CURLOPT_REFERER => 'http://www.google.com/',
        CURLOPT_HTTPHEADER => array("Content-Type: application/JSON", "X-API-KEY: ".$key),
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36',
    ];  
    if ($post) {
        $arr[CURLOPT_POST] = true;
        $arr[CURLOPT_POSTFIELDS] = $post;
    }
    $curl = curl_init();
    curl_setopt_array($curl, $arr);
    $answer = curl_exec($curl);	
    curl_close($curl);
    $answer = json_decode($answer, true);
    return $answer;
}


?>