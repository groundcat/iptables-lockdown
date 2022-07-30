<?php
// Get the ip address from my_ip.json
$json = file_get_contents("./my_ip.json");
$data = json_decode($json, true);
$ip = $data['ip'];
$timestamp = $data['timestamp'];

echo "The stored IP is $ip .\n";
echo "It was last updated at " . date("Y-m-d H:i:s", $timestamp) . "\n";
