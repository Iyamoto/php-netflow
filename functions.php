<?php

/*
 * Functions for PHP-NetFlow project
 */
mb_internal_encoding("UTF-8");

function action($emails, $src_ip, $type, $evidence) {
    $subject = "Detected suspicious IP: $src_ip";
    $text = implode("\n", $evidence);
    $body = $type . "\n" . $text;
    foreach ($emails as $email) {
        $results[] = mail($email, $subject, $body);
    }
    return $results;
}

function get_netflow($command) {
    $results = shell_exec($command); //exeCute 
    $data = str_to_array($results);
    return $data;
}

function str_to_array($str) {
    $str = trim($str);
    $lines = explode("\r\n", $str);
    if (sizeof($lines) == 1)
        $lines = explode("\n", $str);
    for ($i = 1; $i < sizeof($lines) - 4; $i++) {
        $elements[] = explode(',', $lines[$i]);
    }
    if (sizeof($elements) > 0)
        return $elements;
    else
        return false;
}

function save_json($fn, $data) {
    $json = json_encode($data);
    $gz = gzcompress($json);
    //var_dump(strlen($json), strlen($gz));
    return file_put_contents($fn, $gz);
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

function check_whitelist($ip, &$whitelist) {
    foreach ($whitelist as $white_ip) {
        if ($ip == $white_ip)
            return true;
    }
    return false;
}

function get_lastmodified_file($dir) {
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $files[filemtime($dir . DIRECTORY_SEPARATOR . $file)] = $file;
            }
        }
        closedir($handle);
        ksort($files);
        $reallyLastModified = end($files);
        return $reallyLastModified;
    }
    else
        return false;
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

function date_from_filename($filename) {//TODO put regex here
    $needle = str_replace('daily', '', $filename);
    $needle = str_replace('archive', '', $needle);
    $needle = str_replace('.html', '', $needle);
    $needle = str_replace('.gz', '', $needle);
    return $needle;
}

//Load from html template 
function load_from_template($filename) {
    $html = file_get_contents($filename);
    return $html;
}

function build_html_page($daily_file, $page_path, $page_filename, $tpl_name) {
    global $tmp_dir;
    global $tpl_dir;
    global $web_dir;
    $daily_db_file = $tmp_dir . DIRECTORY_SEPARATOR . $daily_file;
    $index_template_file = $tpl_dir . DIRECTORY_SEPARATOR . $tpl_name;
    $block_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'block.html';
    $table_row_template_file = $tpl_dir . DIRECTORY_SEPARATOR . 'table-row.html';
    //Read daily data
    $daily = read_db_from_file($daily_db_file);
    if ($daily) { //Daily db exists
        $daily_size = sizeof($daily);
        echo "[+] Read $daily_size daily blocks\n";
        $html_index_tpl = load_from_template($index_template_file);
        $html_block_tpl = load_from_template($block_template_file);
        $html_table_row_tpl = load_from_template($table_row_template_file);
        $html_blocks = '';
        foreach ($daily as $ip => $types) {
            $html_block_ip = str_replace('$ip', $ip, $html_block_tpl);
            foreach ($types as $type => $evidences) {
                $html_block_type = str_replace('$type', $type, $html_block_ip);
                $table = '';
                $evidence_counter = 0;
                $evidences_reverse = array_reverse($evidences);
                foreach ($evidences_reverse as $evidence_str) {
                    $evidence = explode("\t", trim($evidence_str));
                    $tr = str_replace('$time', $evidence[0], $html_table_row_tpl);
                    $tr = str_replace('$dst_ip', $evidence[1], $tr);
                    $tr = str_replace('$packets', $evidence[2], $tr);
                    $tr = str_replace('$bytes', $evidence[3], $tr);
                    //$table = $tr . "\n" . $table;
                    $table .= $tr . "\n";
                    $evidence_counter++;
                    if ($evidence_counter > 9)
                        break;
                }

                $html_block = str_replace('$table', $table, $html_block_type);
            }
            $html_blocks = $html_block . "\n" . $html_blocks;
        }
        $html_blocks = preg_replace('|<hr>$|', '', $html_blocks);
        $html = str_replace('$blocks', $html_blocks, $html_index_tpl);
        $today = date_from_filename($page_filename);
        if ($today == 'index')
            $today = date("Y-m-d"); //FIXME
        $html = str_replace('$today', $today, $html);
        $where_to_write = $page_path . DIRECTORY_SEPARATOR . $page_filename;
        if (file_put_contents($where_to_write, $html)) {
            echo "[+] File $where_to_write saved\n";
            return true;
        }
    } else { //Daily db is empty
        return false;
    }
}

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

?>
