#!/bin/sh
echo "added $1 to http whitelist"
echo $1 >> /etc/squid/http.txt
service squid reload
