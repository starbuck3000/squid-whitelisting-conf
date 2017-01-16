# squid-whitelisting-conf
This repository includes a configuration example for setting up a Squid proxy configured for whitelisting and HTTPS enforcing access.

Included:
- squid.conf (Squid configuration file)
- *.txt (example whitelisting files)
- add.sh (example script to add new entries into the whitelists)

Installation:
- copy squid.conf and all .txt files.
- read them carefully (do not trust me!)
- verify that folders/locations match your installation, in particular 1) the whitelisting files 2) the logs
- check the logs directives. This configuration will generate 3 logs: 1) errors 2) squid logs 3) common Apache logs.



