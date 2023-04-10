# squid-whitelisting-conf
This repository includes a configuration example for setting up a Squid proxy configured for whitelisting and HTTPS enforcing access.

Included:
- squid.conf : Squid configuration file
- *.txt : example whitelisting files
- add*.sh : example scripts to add new entries into the whitelists
- proxy.php : web interface for unblocking traffic

## Installation
- READ ALL FILES carefully (never blindly trust someone who offers scripts for free!)
- Once you're convinced, copy squid.conf and all .txt files in your squid configuration folder (i.e.: /etc/squid/)
- Verify that folders/locations match your installation, in particular 1) the whitelisting files 2) the logs
- Check the logs directives. This configuration will generate 3 logs: 1) errors 2) squid logs 3) common Apache logs.
- Enable the scripts (chmod +x add*.sh) : if you want to execute it (don't forget to verify its content first!)
- run "squid -k parse -f /etc/squid/squid.conf" to check for configuration errors
- execute "service squid reload" to activate the new configuration
- if you want to use a web interface, the proxy.php file provides a good starting point

## Examples:
- ./addhttp.sh .mydomain.com <-- will grant requests to *.mydomain.com in your whitelist
- ./addhttp.sh www.mydomain.com <-- will grant requests specifically to www.mydomain.com in your whitelist
- ./addhttps.sh www.mysecuredomain.com <-- will grant requests through https:// only
- ./addip.sh 1.2.3.4 <-- will grant requests to the IP address 
- ./addips.sh 5.6.7.8 <-- same as above, through https:// only
- etc.

## Using proxy.php
- The proxy.php file lists all recently blocked entries and generates clickable links to unlock a specific host (http or https)
- Clicking an unlock link will not work by default: the web server needs to be granted privileges to:
  - send the 'reload' command to Squid through sudo (solved by granting the web server access to the command through the sudoers)
  - add new entries to the allowlists (solved using standard *nix privileges)

example using visudo:
```
Cmnd_Alias  RELOADSQUID= /usr/sbin/service squid reload
www-data ALL=NOPASSWD: RELOADSQUID
```

## Performance
I don't know the limits of this configuration. I am currently running it for 5 users, with files containing more than 15'000 entries. No slowdowns at all.

## Things you can improve
1) Read the documentation about the caching directives. You may want to not cache anything at all, or alternatively, cache more things such as heavy objects.
2) running "tail -f /var/log/squid/access.log | grep DENIED" will help you monitor any request denied by your squid

## Things you can help
* I'm lazy as f. when it comes to improving my shell scripting skills. Wish I could just type: "./add http|https|ip|ips VALUE" and the script would know precisely in which file to add the entry.
* Adding entries still requires shell access. Maybe having a .php file that:
 * parses the recent Denied entries
 * lists them and exposes a 1-click button that does the script update and service reload 

