<?php

/*
 *   More info about an IP
 */
$exec_time = microtime(true);
echo "\n[+] Started\n";

$ip = htmlentities($_GET['ip'], ENT_QUOTES, 'UTF-8');
$last_daily = get_lastmodified_file('tmp');
echo $ip;
var_dump($last_daily);

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";

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
