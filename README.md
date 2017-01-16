# squid-whitelisting-conf
This repository includes a configuration example for setting up a Squid proxy configured for whitelisting and HTTPS enforcing access.

Included:
- squid.conf (Squid configuration file)
- *.txt (example whitelisting files)
- add.sh, addssl.sh (example scripts to add new entries into the whitelists)

Installation:
- READ ALL FILES carefully (never blindly trust someone who offers scripts for free!)
- Once you're convinced, copy squid.conf and all .txt files in your squid configuration folder (i.e.: /etc/squid/)
- Verify that folders/locations match your installation, in particular 1) the whitelisting files 2) the logs
- Check the logs directives. This configuration will generate 3 logs: 1) errors 2) squid logs 3) common Apache logs.
- Enable the scripts (chmod +x add*.sh) : if you want to execute it (don't forget to verify its content first!)

Usage:
- don't forget to run "squid -k parse" to check for configuration errors
- execute "service squid reload" to activate the new configuration

Example commands:
- tail -f /var/log/squid/access.log | grep DENIED  <-- this command will help you monitor any request denied by your squid
- ./add.sh .mydomain.com <-- will grant requests to *.mydomain.com in your whitelist
- ./add.sh www.mydomain.com <-- will grant requests specifically to www.mydomain.com in your whitelist
- ./addsh.sh www.mysecuredomain.com <-- will grant requests only through https:// 
- etc.
