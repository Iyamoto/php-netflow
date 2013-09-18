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
$stats_deep = 3; //days
$ip_deep = 5;
//Get daily db files list
foreach ($last_daily_files as $daily_file) {
    if (stristr($daily_file, 'daily'))
        $file_names[] = $daily_file;
}
rsort($file_names);
//Cycle throw last $stats_deep days
for ($day_counter = 0; $day_counter < $stats_deep; $day_counter++) {
    echo "[+] Processing file: $file_names[$day_counter]\n";
    $daily_db_file = $tmp_dir . DIRECTORY_SEPARATOR . $file_names[$day_counter];
    $daily = read_db_from_file($daily_db_file);
    if ($daily) { //Daily db exists
        $daily_size = sizeof($daily);
        echo "[+] Read $daily_size daily blocks\n";
        foreach ($daily as $ip => $types) {
            foreach ($types as $type => $evidences) {
                $top_ip[$ip] = sizeof($evidences);
            }
        }
        arsort($top_ip);
        $ip_counter = 0;
        foreach ($top_ip as $tip=>$count) {
            var_dump($tip);
            foreach($daily[$tip] as $types=>$evidences){
                var_dump($types);
                $activity = sizeof($evidences);
                var_dump($activity);
            }
            $ip_counter++;
            if ($ip_counter >= $ip_deep)
                break;
        }

        unset($top_ip);
    }
}

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
