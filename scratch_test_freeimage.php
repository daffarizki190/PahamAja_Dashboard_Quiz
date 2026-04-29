<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://freeimage.host/api/1/upload');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'key' => '6d207e02198a847aa98d0a2a901485a5',
    'action' => 'upload',
    'source' => base64_encode(file_get_contents('public/favicon.ico'))
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
echo $res;
