<?php

/*
 *   More info about an IP
 */
$exec_time = microtime(true);
echo "<br>[+] Started<br>";
$tmp_dir = 'db';
$ip = htmlentities($_GET['ip'], ENT_QUOTES, 'UTF-8');

echo $ip . '<br>';
$daily_files = get_dir_list($tmp_dir);
$daily_files_size = sizeof($daily_files);
if ($daily_files_size < 1)
    exit("[-] Daily files not found<br>");
foreach ($daily_files as $daily_file) {
    if (stristr($daily_file, 'daily'))
        $file_names[] = $daily_file;
}
rsort($file_names);
$daily = read_db_from_file($tmp_dir. DIRECTORY_SEPARATOR .$file_names[0]);
if ($daily) { //Daily db exists
    $daily_size = sizeof($daily);
    echo "[+] Read $daily_size daily blocks<br>";
    var_dump($daily);
}

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.<br>";

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
