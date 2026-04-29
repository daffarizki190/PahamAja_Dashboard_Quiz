<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://freeimage.host/api/1/upload');
curl_setopt($ch, CURLOPT_POST, 1);
$cfile = new CURLFile('C:\Users\MyBook Hype AMD\.gemini\antigravity\brain\e71fe557-103b-4baf-afd7-6556382de2fd\media__1777486556222.png', 'image/png', 'image.png');
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'key' => '6d207e02198a847aa98d0a2a901485a5',
    'action' => 'upload',
    'source' => $cfile
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
echo $res;
