#!/bin/bash
# CONFIG BEGIN
api="https://example.com/iptables-lockdown/api.php"
cdn="cloudflare"
ip_auth_key=""
ip_auth_tcp="0"  # 0 = disallow all, 1 = IP auth only, 2 = allow all
# CONFIG END
ipv4_url="$api?protocol=ipv4&cdn=$cdn&ip_auth_tcp=$ip_auth_tcp&ip_auth_key=$ip_auth_key"
mkdir -p /opt/iptables-backup
cp /etc/iptables/rules.v4 /opt/iptables-backup
curl -o /etc/iptables/rules.v4 $ipv4_url
iptables-restore < /etc/iptables/rules.v4
