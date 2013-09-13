<?php

/*
 * Example config file for PHP-NetFlow
 * Rename to config.php
 */
$email = 'name@domain.zone';//Where to report

//Path
$netflow_base_dir = '/var/cache/nfdump/flows/live/4217FB546AC8F2E170FB0D58286FF0DD';

$nfdump = '/usr/bin/nfdump';//which nfdump

//NFDump filters
$lan_src = 'src net 10.0/8 or src net 192.168/16';
$lan_dst = 'dst net 10.0/8 or dst net 192.168/16';

//What are we looking for?
$marks[]='proto icmp and icmp-type 8';
$marks[]='dst port 53';
$marks[]='proto tcp and dst port 3389';
$marks[]='proto tcp and dst port 6667';

$num = 10;//Define number of top N

?>
