<?php

$noc_ipv4_rules_url = "https://noc.org/ipv4";
$noc_ipv4_rules_file = "data/noc_ipv4.txt";

$noc_ipv6_rules_url = "https://noc.org/ipv6";
$noc_ipv6_rules_file = "data/noc_ipv6.txt";

$cloudflare_ipv4_rules_url = "https://www.cloudflare.com/ips-v4";
$cloudflare_ipv4_rules_file = "data/cloudflare_ipv4.txt";

$cloudflare_ipv6_rules_url = "https://www.cloudflare.com/ips-v6";
$cloudflare_ipv6_rules_file = "data/cloudflare_ipv6.txt";


function download_with_curl($url, $file) {
    echo "Downloading from $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    $output = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "HTTP code: $http_code\n";
    if ($http_code == 200) {
        $fp = fopen($file, 'w');
        $output = preg_replace("/\n\n/", "\n", $output);  // remove empty line
        $output = preg_replace("/\n$/", "", $output);  // remove last empty line
        fwrite($fp, $output);
        fclose($fp);
        return $output;
    } else {
        return false;
    }
}

download_with_curl($noc_ipv4_rules_url, $noc_ipv4_rules_file);
download_with_curl($noc_ipv6_rules_url, $noc_ipv6_rules_file);
download_with_curl($cloudflare_ipv4_rules_url, $cloudflare_ipv4_rules_file);
download_with_curl($cloudflare_ipv6_rules_url, $cloudflare_ipv6_rules_file);
