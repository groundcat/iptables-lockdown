<?php

/**
 * Validates the format of a CIDR notation string
 * Author: https://gist.github.com/mdjekic/ac1f264e37bddfc63be8a042ced52e64
 *
 * @param string $cidr
 * @return bool
 */
function validateCidr($cidr)
{
    $parts = explode('/', $cidr);
    if(count($parts) != 2) {
        return false;
    }

    $ip = $parts[0];
    $netmask = intval($parts[1]);

    if($netmask < 0) {
        return false;
    }

    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $netmask <= 32;
    }

    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return $netmask <= 128;
    }

    return false;
}

