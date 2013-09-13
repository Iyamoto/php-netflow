<?php

/*
 * Example config file for PHP-NetFlow
 * Rename to config.php
 * Tuned for usage with OSSIM
 */
$emails[] = 'name@domain.zone';//Where to report

//Path
$netflow_base_dir = '/var/cache/nfdump/flows/live/profile_id';
$tmp_dir = '/tmp';

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

$num = 10;//Define number of top N
$dst_ip_lvl = 5;//Action lvl for dst IPs

$debug = false;

$test_results = '';
$test_results2 = '';
?>
