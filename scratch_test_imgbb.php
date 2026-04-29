<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.imgbb.com/1/upload');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'key' => '3b5930e3bb4446f254bca1a562810a95',
    'image' => base64_encode(file_get_contents('C:\Users\MyBook Hype AMD\.gemini\antigravity\brain\e71fe557-103b-4baf-afd7-6556382de2fd\media__1777486556222.png'))
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
echo $res;
