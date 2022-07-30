# Iptables Lockdown API

Deploy a whitelist model on your server for security hardening.

## Features

**Web server whitelist protection**: Allow only the IP addresses originating from Cloudflare or NOC.org CDN nodes

**SSH IP Auth protection**: Allow only the IP address originating from your client with [IP Auth](https://noc.org/help/create-a-whitelist-control-list-using-ssh-ip-authentication/) (block all users by default and whitelist authorized users) - also optionally available for protecting other ports.

## Setup and Usage

### Prerequisites

First, install iptables-persistent. During installation it will ask you if you want to keep current rules–decline.

    sudo apt update
    sudo apt install -y iptables-persistent

### API Server

Deploy all `.php` scripts and the `data` folder to a remote PHP web server. Preferably this should be a different server than Your Server.

Set up a scheduled cron job to run the `cron.php` script every day. This script will update the CDN whitelists from Cloudflare and NOC.org.

    sudo crontab -e
    # cron.php will run every day at midnight
    # 0 0 * * * php /path/to/api/cron.php

For example, your API server URL will be `https://example.com/iptables-lockdown/api.php`.

### IP Auth

#### Option 1. Use NOC.org IP Auth

[Request](https://noc.org/help/create-a-whitelist-control-list-using-ssh-ip-authentication/) for an IP Auth key from NOC.org if you want to use IP Auth.

#### Option 2. Use IP Auth hosted on your own server

A simple IP Auth implementation is included in the `ip_auth` folder. You can use this as a starting point for your own implementation. Some codes in the `api.php` file under the commented line `// IP Auth` are based on the NOC.org IP Auth implementation, so you will need to change them if you want to use a self-hosted implementation.

### Your Server

Deploy `iptables_update.sh` to your server that needs protection.

Edit the `CONFIG` section to your needs. See below.

`api` is your API server. Example: 

    api="https://example.com/iptables-lockdown/api.php"

`cdn` is the CDN provider that you want to whitelist for your web server. Example: 

    cdn="cloudflare"
    # "cloudflare" or "noc" 

`ip_auth_key` is the IP Auth key you obtained from NOC.org. Example: 

    ip_auth_key="fa6492401ee7beb27f74bf19757a89b9c8800ae5"

`ip_auth_tcp` indicates if you want to use IP Auth to open TCP ports 1024:65535 to users authorized by IP Auth. When in doubt, set it at `ip_auth_tcp="0"`. Example: 

    ip_auth_tcp="1"  
    # 0 = disallow all, 1 = IP auth only, 2 = allow all

Create a scheduled cron job to run it every time you want to update the allow list.

    sudo crontab -e
    @daily /path/to/iptables_update.sh

Remember that you can check your current iptables ruleset with `sudo iptables -S` and `sudo iptables -L`.

#### IPv4 Only

Deploy `iptables_update_v4.sh` if you want IPv4 only. Remember to disable IPv6 following the instructions below:

Add these lines to `/etc/sysctl.conf`:

    net.ipv6.conf.all.disable_ipv6 = 1
    net.ipv6.conf.default.disable_ipv6 = 1

Then load your changes:

    $ sudo sysctl -p
    net.ipv6.conf.all.disable_ipv6 = 1
    net.ipv6.conf.default.disable_ipv6 = 1

