<?php

/*
 * Example config file for PHP-NetFlow
 * Rename to config.php
 * Tuned for usage with OSSIM
 */

//$emails and $netflow_base_dir must be set 
$emails[] = 'name@domain.zone';//Whom to report
$netflow_base_dir = '/var/cache/nfdump/flows/live/profile_id';//Where to look for netflow data
$web_dir = '/var/www/botolovka';
$tmp_dir = '/tmp/phpnetflow';
$tpl_dir = 'ru-tpl';
$today = date("Y-m-d");
if (!is_dir($tmp_dir))
    mkdir($tmp_dir);
$db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'onerun.gz';
$daily_db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'daily'.$today.'.gz';
$nfdump = '/usr/bin/nfdump';//which nfdump


//NFDump filters
$lan_src = 'src net 10.0/8 or src net 192.168/16';
$lan_dst = 'dst net 10.0/8 or dst net 192.168/16';

//What are we looking for?
$marks[]='proto icmp and icmp-type 8';
$marks[]='dst port 53';
$marks[]='proto tcp and dst port 3389';//something like Morto
$marks[]='proto tcp and dst port 6667';//bots looking for C&C?
$marks[]='proto tcp and dst port 25';//Spam bots
$marks[]='proto tcp and dst port 445';
$marks[]='proto tcp and dst port 9050';//Tor
$marks[]='proto tcp and (dst port 3128 or dst port 8118)';//Proxy
$marks[]='proto tcp and dst port 22';//ssh scans
$marks[]='proto tcp and dst port 23';//telnet scans
$marks[]='proto tcp and dst port 21';//ftp

$whitelist['proto icmp and icmp-type 8'][]='10.10.10.1';//White IP for first mark
$whitelist['dst port 53'][]='10.10.10.2';//White IP for second mark
//TODO add netmask support for whitelist

$num = 10;//Define number of top N
$dst_ip_lvl = 5;//Action lvl for dst IPs

$debug = false;

$test_results = '';
$test_results2 = '';
?>
