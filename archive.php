<?php

/*
 * HTML archive reports generator for PHP-NetFlow
 */

$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

$daily_files = get_dir_list($tmp_dir);
$daily_files_size = sizeof($daily_files);
if ($daily_files_size < 1)
    exit("[-] Daily files not found\n");
$archive_files = get_dir_list($web_dir . DIRECTORY_SEPARATOR . 'archive');
$archive_files_size = sizeof($archive_files);
$tpl_path = 'archive' . DIRECTORY_SEPARATOR . 'archive.html';

foreach ($daily_files as $daily_file) {
    if (stristr($daily_file, 'daily')) {
        $needle = date_from_filename($daily_file);
        if ($needle == $today)
            continue;
        if ($archive_files_size > 0) {
            $found = false;
            foreach ($archive_files as $archive_file) {
                $haystack = date_from_filename($archive_file);
                if ($needle == $haystack) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $do_archive[] = $daily_file;
                build_html_page($daily_file, 'archive' . $needle . '.html', $tpl_path);
            }
        } else {
            $do_archive[] = $daily_file;
            build_html_page($daily_file, 'archive' . $needle . '.html', $tpl_path);
        }
    }
}

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
