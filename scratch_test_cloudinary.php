<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.cloudinary.com/v1_1/demo/image/upload');
curl_setopt($ch, CURLOPT_POST, 1);
$cfile = new CURLFile('C:\Users\MyBook Hype AMD\.gemini\antigravity\brain\e71fe557-103b-4baf-afd7-6556382de2fd\media__1777486556222.png', 'image/png', 'image.png');
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'file' => $cfile,
    'upload_preset' => 'docs_upload_example_us'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
curl_close($ch);
echo $res;
