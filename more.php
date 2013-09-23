<?php

/*
 *   More info about an IP
 */
$exec_time = microtime(true);
echo "<br>[+] Started<br>";

$ip = htmlentities($_GET['ip'], ENT_QUOTES, 'UTF-8');

echo $ip.'<br>';
$daily_files = get_dir_list('db');
$daily_files_size = sizeof($daily_files);
if ($daily_files_size < 1)
    exit("[-] Daily files not found<br>");
foreach ($daily_files as $daily_file) {
    if (stristr($daily_file, 'daily'))
        $file_names[] = $daily_file;
}
rsort($file_names);

var_dump($file_names[0]);

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
?>
