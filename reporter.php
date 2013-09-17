<?php

/*
 * HTML reports generator for PHP-NetFlow
 */

$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";
$page_path = $web_dir;
$page_filename = 'index.html';
build_html_page('daily' . $today . '.gz', $page_path, $page_filename, 'index.html');

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
