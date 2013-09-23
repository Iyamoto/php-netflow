<?php

/*
 *   More info about an IP
 */
$exec_time = microtime(true);
$tmp_dir = 'db';
$tpl_dir = 'ru-tpl';
$ip = htmlentities($_GET['ip'], ENT_QUOTES, 'UTF-8');
$daily_files = get_dir_list($tmp_dir);
$daily_files_size = sizeof($daily_files);
if ($daily_files_size < 1)
    exit("[-] Daily files not found<br>");
foreach ($daily_files as $daily_file) {
    if (stristr($daily_file, 'daily'))
        $file_names[] = $daily_file;
}
rsort($file_names);

$days_counter = 0;
foreach ($file_names as $daily_file) {
    $daily_db_file = $tmp_dir . DIRECTORY_SEPARATOR . $daily_file;
    $daily = read_db_from_file($daily_db_file);
    if ($daily) { //Daily db exists
        if (isset($daily[$ip])) {//IP is in the db
            foreach ($daily[$ip] as $type => $evidences) {
                foreach ($evidences as $evidence) {
                    $global[$ip][$type][] = $evidence;
                }
            }
        }
    }
    $days_counter++;
    if($days_counter>14) break;
}

$html = build_html_page($global, $ip);
echo $html;

function build_html_page($daily, $ip) {
    global $tpl_dir;
    $index_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'more.html';
    $block_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'more-blocks.html';
    $table_row_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'table-row.html';
    if ($daily) { //Daily db exists
        $daily_size = sizeof($daily);
        $html_index_tpl = load_from_template($index_template_file);
        $html_block_tpl = load_from_template($block_template_file);
        $html_table_row_tpl = load_from_template($table_row_template_file);
        $html_blocks = '';
        foreach ($daily[$ip] as $type => $evidences) {
            $html_block_type = str_replace('$type', $type, $html_block_tpl);
            $table = '';
            $evidence_counter = 0;
            foreach ($evidences as $evidence_str) {
                $evidence = explode("\t", trim($evidence_str));
                $tr = str_replace('$time', $evidence[0], $html_table_row_tpl);
                $tr = str_replace('$dst_ip', $evidence[1], $tr);
                $tr = str_replace('$packets', $evidence[2], $tr);
                $tr = str_replace('$bytes', $evidence[3], $tr);
                $table .= $tr . "\n";
                $evidence_counter++;
                if ($evidence_counter > 100)
                    break;
            }
            $html_block = str_replace('$table', $table, $html_block_type);
            $html_blocks = $html_block . "\n" . $html_blocks;
        }
        $html_blocks = preg_replace('|<hr>$|', '', $html_blocks);
        $html = str_replace('$blocks', $html_blocks, $html_index_tpl);
        $html = str_replace('$ip', $ip, $html);
        return $html;
    } else { //Daily db is empty
        return false;
    }
}

function load_from_template($filename) {
    $html = file_get_contents($filename);
    return $html;
}

function get_dir_list($dir) {
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $files[] = $file;
            }
        }
        closedir($handle);
        return $files;
    }
    else
        return false;
}

function read_db_from_file($filename) {
    if (file_exists($filename)) {
        $json = load_json($filename);
        if ($json)
            return $json;
        else
            return false;
    } else {
        echo "[-] $filename not found\n";
        return false;
    }
}

function load_json($fn) {
    $gz = file_get_contents($fn);
    if ($gz) {
        $json = gzuncompress($gz);
        $data = json_decode($json, true);
        return $data;
    } else {
        echo "[-] Cant load file $fn\n";
        return false;
    }
}

?>
