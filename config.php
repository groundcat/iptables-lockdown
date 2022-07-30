<?php

const V4_DEFAULT_RULE_BEGINNING = "
*filter
# Allow all loopback (lo0) traffic and drop all traffic to 127/8 that doesn't use lo0
-A INPUT -i lo -j ACCEPT
-A OUTPUT -o lo -j ACCEPT
#  Accept all established inbound connections
-A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
#  Allow all outbound traffic - you can modify this to only allow certain traffic
-A OUTPUT -j ACCEPT
";

const V6_DEFAULT_RULE_BEGINNING = "
*filter
# Allow all loopback (lo0) traffic and drop all traffic to ::1 that doesn't use lo0
-A INPUT -i lo -j ACCEPT
-A OUTPUT -o lo -j ACCEPT
# Accept all established inbound connections
-A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
# Allow all outbound traffic - you can modify this to only allow certain traffic
-A OUTPUT -j ACCEPT
";

const V4_DEFAULT_RULE_ENDING = "
# Reject all other inbound - default deny unless explicitly allowed policy
-A INPUT -j REJECT
-A FORWARD -j REJECT
COMMIT
";

const V6_DEFAULT_RULE_ENDING = "
#  Reject all other inbound - default deny unless explicitly allowed policy
-A INPUT -j REJECT
-A FORWARD -j REJECT
COMMIT
";

