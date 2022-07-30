<?php

include("config.php");
include("functions.php");

$rule = "";

// GET variables
$protocol = $_GET["protocol"];  // "ipv4" or "ipv6"
$cdn = $_GET["cdn"];  // (optional) "noc" or "cloudflare"
$ip_auth_key = $_GET["ip_auth_key"];  // (optional) key from noc.org IP auth
$ip_auth_tcp = $_GET["ip_auth_tcp"];  // (optional) if set to 1, allow 1024:65535 TCP ports for the authed IP
$cdn_and_protocol = $cdn . "_" . $protocol;


// Default rules beginning
switch ($protocol) {
    case "ipv4":
        $rule .= V4_DEFAULT_RULE_BEGINNING;
        break;
    case "ipv6":
        $rule .= V6_DEFAULT_RULE_BEGINNING;
        break;
    default:
        echo "Invalid protocol: $protocol\n";
        exit(1);
}


// Rules for web server
$rule .= "# Rules for web server\n";
switch ($cdn_and_protocol) {
    case "noc_ipv4":
        $ips = file_get_contents("data/noc_ipv4.txt");
        break;
    case "noc_ipv6":
        // TODO: NOC hasn't supported ipv6 yet
        $ips = false;
        $rule .= "-A INPUT -p tcp -m multiport --dports 80,443 -j DROP\n";
        break;
    case "cloudflare_ipv4":
        $ips = file_get_contents("data/cloudflare_ipv4.txt");
        break;
    case "cloudflare_ipv6":
        $ips = file_get_contents("data/cloudflare_ipv6.txt");
        break;
    case "cloudflare_and_noc_ipv4":
        $ips = file_get_contents("data/cloudflare_ipv4.txt");
        $ips .= "\n" . file_get_contents("data/noc_ipv4.txt");
        break;
    default:
        $ips = false;
        $rule .= "# Accept all IPs to web server\n-A INPUT -p tcp --dport 80 -j ACCEPT\n-A INPUT -p tcp --dport 443 -j ACCEPT\n";
};

if ($ips) {
    foreach (explode("\n", $ips) as $ip) {
        if (validateCidr($ip) || inet_pton($ip)) {
            $rule .= "-A INPUT -s $ip -p tcp -m multiport --dports 80,443 -j ACCEPT\n";
        } else {
            echo "Invalid IP obtained from CDN's IP list: $ip\n";
            exit(1);
        }
    };
    $rule .= "-A INPUT -p tcp -m multiport --dports 80,443 -j DROP\n";
}


// IP Auth
if (strlen($ip_auth_key) == 40) {
    // NOC.org Auth key
    $ip_auth_server_url = "https://my.noc.org/ipauth/server?code=" . $ip_auth_key;
    $auth_response = file_get_contents($ip_auth_server_url);
    $ip_auth_valid = preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $auth_response, $matches);  // Parse IPv4 address from the response
    $ip_auth_ip = $matches[0];
} else {
    $ip_auth_valid = false;
    $ip_auth_ip = "";
}


// SSH rules
if ($protocol == "ipv6") {
    $rule .= "# Disallow all IPs to SSH through IPv6\n";
    $rule .= "-A INPUT -p tcp --dport 22 -j DROP\n";
} elseif ($ip_auth_valid) {
    $rule .= "# SSH IP Auth whitelist\n";
    $rule .= "-A INPUT -s $ip_auth_ip -p tcp -m multiport --dports 22 -j ACCEPT\n";
    $rule .= "-A INPUT -p tcp --dport 22 -j DROP\n";
} else {
    // Allow all IPs to SSH
    $rule .= "# Allow all IPs to SSH\n";
    $rule .= "-A INPUT -p tcp -m tcp --dport 22 -j ACCEPT\n";
}


// TCP whitelist rules
if ($ip_auth_tcp == 1 && $ip_auth_valid && $protocol == "ipv4") {
    $rule .= "# TCP IP Auth whitelist\n";
    $rule .= "-A INPUT -s $ip_auth_ip -p tcp -m multiport --dports 1024:65535 -j ACCEPT\n";
}

if ($ip_auth_tcp == 2) {
    $rule .= "# TCP IP Allow All\n";
    $rule .= "-A INPUT -p tcp -m multiport --dports 1024:65535 -j ACCEPT\n";
}


// Default rules ending
switch ($protocol) {
    case "ipv4":
        $rule .= V4_DEFAULT_RULE_ENDING;
        break;
    case "ipv6":
        $rule .= V6_DEFAULT_RULE_ENDING;
        break;
    default:
        echo "Invalid protocol: $protocol\n";
        exit(1);
}


header('Content-Type: application/text');
echo $rule;
