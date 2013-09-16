<?php

/*
 * Functions for PHP-NetFlow project
 */
mb_internal_encoding("UTF-8");

function action($emails, $src_ip, $type, $evidence) {
    $subject = "Detected evil IP: $src_ip";
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

?>
