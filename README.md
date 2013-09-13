php-netflow
===========

PHP NetFlow anomaly detector
Finds malware/virus bots in LAN
Based on NFDump
Tested with OSSIM SEIM

Usage
=====
git clone https://github.com/Iyamoto/php-netflow.git
cp sample-config.php config.php
replace email, path etc in config.php
add starter.sh to cron

Design
======
read config
get date, last netflow file name (nfcapd.date)
check nfcapd for interesting traffic with nfdump
investigate suspicious IPs
mail to master

Detection
=========
Looking for trafic from sigle LAN IP to multiple outside IPs 
echo requests: proto icmp and icmp-type 8
DNS requests: dst port 53
RDP: proto tcp and dst port 3389
