<?php

/*
 * HTML archive reports generator for PHP-NetFlow
 */

$exec_time = microtime(true);
require_once 'config.php';
echo "\n[+] Started\n";

$daily_files = get_dir_list($tmp_dir);
$daily_files_size = sizeof($daily_files);
if ($daily_files_size < 1)
    exit("[-] Daily files not found\n");
$archive_files = get_dir_list($web_dir . DIRECTORY_SEPARATOR . 'archive');
$archive_files_size = sizeof($archive_files);
$tpl_path = 'archive' . DIRECTORY_SEPARATOR . 'archive.html';
$page_path = $web_dir . DIRECTORY_SEPARATOR . 'archive';

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
                $page_filename = 'archive' . $needle . '.html';
                build_html_page($daily_file, $page_path, $page_filename, $tpl_path);
            }
        } else {
            $do_archive[] = $daily_file;
            $page_filename = 'archive' . $needle . '.html';
            build_html_page($daily_file, $page_path, $page_filename, $tpl_path);
        }
    }
}

$index_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'index.html';
$link_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'link.html';
$html_index_tpl = load_from_template($index_template_file);
$link_index_tpl = load_from_template($link_template_file);
$html_links = '';
foreach ($daily_files as $daily_file) {
    if (stristr($daily_file, 'daily')) {
        $date = date_from_filename($daily_file);
        if ($date == $today)
            continue;
        $ahref = 'archive' . $date . '.html';
        $atext = $date;
        $html_link = str_replace('$ahref', $ahref, $link_index_tpl);
        $html_link = str_replace('$atext', $atext, $html_link);
        $html_links .= $html_link . "\n";
    }
}
$html = str_replace('$links', $html_links, $html_index_tpl);
$where_to_write = $page_path . DIRECTORY_SEPARATOR . 'index.html';
if (file_put_contents($where_to_write, $html)) {
    echo "[+] File $where_to_write saved\n";
}

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
