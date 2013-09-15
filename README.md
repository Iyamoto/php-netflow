php-netflow
===========
PHP NetFlow anomaly detector
Finds malware/virus bots in LAN
Based on NFDump
Tested in OSSIM SEIM
PHP 5.3, 5.4

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
Data structures
onerun.json
one_run[] -> src ip 
          -> mark (type of traffic)
          -> evidences[] = $time."\t".$dst_ip."\t".$packets."\t".$bytes."\n"

daily json
daily[IP] -> marks[] -> evidences[]

Activity checker (checker.php)
    read config
    get date, last netflow file name (OSSIM:.../date/nfcapd.datetime)
    cycle throw marks(aka types of traffic)
        check nfcapd file for interesting traffic with nfdump
        investigate suspicious IPs
    save to json

Filter
load from one_run json
load from daily json
cycle one_run data
    check if ip uniq (absent in daily db)
        insert to daily db
        start action()
    else 
        add new data to daily[IP]
            
save daily json


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
SMTP: proto tcp and dst port 25
445?

Filter Syntax
=============
Syntax is similar to tcpdump
Look at http://nfdump.sourceforge.net/ for more info.