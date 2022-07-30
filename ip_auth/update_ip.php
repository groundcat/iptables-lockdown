<?php

// Validate the key by comparing it to `my_key.json`
include("validate_key.php");

// Get IP address from the user
$ip = $_SERVER['REMOTE_ADDR'];

// Write the timestamp and ip to json
$json = json_encode(array("timestamp" => time(), "ip" => $ip));
file_put_contents("./my_ip.json", $json);

// Show result
echo "The stored IP is $ip\n";
