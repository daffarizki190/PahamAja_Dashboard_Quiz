<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://ui-avatars.com/api/?name=FADHUR+ROHMAN&background=random&color=fff&size=256&bold=true");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$res = curl_exec($ch);
echo "Status: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo substr($res, 0, 500);
