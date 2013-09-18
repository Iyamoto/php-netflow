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

$page_path = $web_dir . DIRECTORY_SEPARATOR . 'stats';
$index_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'stats' . DIRECTORY_SEPARATOR . 'index.html';
$block_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'stats' . DIRECTORY_SEPARATOR . 'block.html';
$table_row_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'stats' . DIRECTORY_SEPARATOR . 'table-row.html';
$html_index_tpl = load_from_template($index_template_file);
$html_block_tpl = load_from_template($block_template_file);
$table_row_index_tpl = load_from_template($table_row_template_file);
$html_table_rows = '';

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

$html_blocks = '';
//Cycle throw last $stats_deep days
for ($day_counter = 0; $day_counter < $stats_deep; $day_counter++) {
    echo "[+] Processing file: $file_names[$day_counter]\n";
    $day = date_from_filename($file_names[$day_counter]);
    $html_block_day = str_replace('$day', $day, $html_block_tpl);
    if($day_counter==0) $archlink = '../index.html';
    else $archlink = '../archive/archive'.$day.'.html';
    $html_block_day = str_replace('$archlink', $archlink, $html_block_day);

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
        $table = '';
        foreach ($top_ip as $tip => $count) {
            foreach ($daily[$tip] as $type => $evidences) {
                $activity = sizeof($evidences);
                $tr = str_replace('$ip', $tip, $table_row_index_tpl);
                $tr = str_replace('$mark', $type, $tr);
                $tr = str_replace('$activity', $activity, $tr);
                $table .= $tr . "\n";
            }
            $ip_counter++;
            if ($ip_counter >= $ip_deep)
                break;
        }
        $html_block = str_replace('$table', $table, $html_block_day);

        unset($top_ip);
    }
    //$html_blocks = $html_block . "\n" . $html_blocks;
    $html_blocks .= $html_block . "\n";
}

$html_blocks = preg_replace('|<hr>$|', '', $html_blocks);
$html = str_replace('$blocks', $html_blocks, $html_index_tpl);
$where_to_write = $page_path . DIRECTORY_SEPARATOR . 'index.html';
if (file_put_contents($where_to_write, $html)) {
    echo "[+] File $where_to_write saved\n";
    return true;
}

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
