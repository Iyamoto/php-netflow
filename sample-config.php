<?php

/*
 * Example Config file for PHP-NetFlow
 * Rename to config.php
 */
$email = 'name@domain.zone';//Where to report

$netflow_base_dir = '/var/cache/nfdump/flows/live/4217FB546AC8F2E170FB0D58286FF0DD';
$netflow_current_dir = '2013-09-13';//TODO add date detection
$netflow_last_file = 'nfcapd.1';//FIXME add last file detection 

$nfdump = '/usr/bin/nfdump';

//NFDump filters
$lan_src = 'src net 10.0/8 or src net 192.168/16';
$lan_dst = 'dst net 10.0/8 or dst net 192.168/16';
?>
