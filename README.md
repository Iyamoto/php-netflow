php-netflow
===========

PHP NetFlow anomaly detector
Finds malware/virus bots in LAN
Based on NFDump
Tested with OSSIM SEIM

Usage
=====
aptitude install git (if needed)
git clone https://github.com/Iyamoto/php-netflow.git
cp sample-config.php config.php
replace email, netflow_base_dir in config.php
php checker.php
add starter.sh to cron

Design
======
Activity checker (checker.php)
    read config
    load from json
    get date, last netflow file name (nfcapd.date) -> path to nfcapd
    cicle throw marks
        check nfcapd for interesting traffic with nfdump
        investigate suspicious IPs
        check time for last report
            mail to master
    save to json

Reporter (reporter.php)
    read config
    load from json
    
    form main web page
    save web page


Detection
=========
Looking for trafic from sigle LAN IP to multiple outside IPs 
echo requests: proto icmp and icmp-type 8
DNS requests: dst port 53
RDP: proto tcp and dst port 3389

Filter Syntax
=============
Syntax is similar to tcpdump
Look at http://nfdump.sourceforge.net/ for more info.