<?php

/*
 * Statistics reports generator for PHP-NetFlow
 */

$exec_time = microtime(true);
require_once 'config.php';
echo "\n[+] Started\n";

$daily_files = get_dir_list($tmp_dir);
$daily_files_size = sizeof($daily_files);
if ($daily_files_size < 1)
    exit("[-] Daily files not found\n");
$tpl_path = 'stats' . DIRECTORY_SEPARATOR . 'index.html';
$page_path = $web_dir . DIRECTORY_SEPARATOR . 'stats';

$last_daily_files = array_reverse($daily_files);
$day_counter = 0;
$stats_deep = 3;//days
foreach ($last_daily_files as $daily_file) {
    if (stristr($daily_file, 'daily')) {
        $needle = date_from_filename($daily_file);
        var_dump($daily_file);
        $day_counter++;
        if($day_counter>=$stats_deep) break;
    }
}

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
