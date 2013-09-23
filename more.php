<?php

/*
 *   More info about an IP
 */
$exec_time = microtime(true);
require_once 'config.php';
echo "\n[+] Started\n";

$ip = htmlentities($_GET['ip'], ENT_QUOTES, 'UTF-8');
echo $ip;

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
