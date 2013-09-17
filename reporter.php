<?php

/*
 * HTML reports generator for PHP-NetFlow
 */

$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

build_html_page('daily' . $today . '.gz', 'index.html', 'index.html');

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
