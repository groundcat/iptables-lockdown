<?php

// Validate the key by comparing it to `my_key.json`
include("validate_key.php");

// Get the ip address from my_ip.json
$json_ip = file_get_contents("./my_ip.json");
$data = json_decode($json_ip, true);
$ip = $data['ip'];
$timestamp = $data['timestamp'];

// Show result
echo "The stored IP is $ip .\n";
echo "It was last updated at " . date("Y-m-d H:i:s", $timestamp) . "\n";
