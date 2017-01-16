#!/bin/sh
echo "added $1 to TLS/SSL list"
echo $1 >> /etc/squid/ssl.txt
service squid reload
