#!/bin/sh
echo "added $1 to TLS/SSL IP address list"
echo $1 >> /etc/squid/httpsip.txt
service squid reload
