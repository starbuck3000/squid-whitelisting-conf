#!/bin/sh
echo "added $1 to whitelist"
echo $1 >> /etc/squid/whitelist.txt
service squid reload
