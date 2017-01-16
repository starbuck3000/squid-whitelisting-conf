#!/bin/sh
echo "added $1 to http IP address list"
echo $1 >> /etc/squid/httpip.txt
service squid reload
