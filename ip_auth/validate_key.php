<?php

// Get the key
$json_key = file_get_contents("./my_key.json");
$data = json_decode($json_key, true);
$key = $data['key'];

// Compare the key with the one in the json file
if ($_GET['key'] != $key) {
    echo "Invalid key\n";
    exit(1);
}
