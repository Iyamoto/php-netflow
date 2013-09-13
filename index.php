<?php

/*
 * NetFlow anomaly detector
 * Finds malware/virus bots in LAN
 */

mb_internal_encoding("UTF-8");
$exec_time = microtime(true);
require_once 'config.php';
echo "\n[+] Started\n";

//Get netflow data
$netflow_current_dir = '2013-09-13';//TODO add date detection
$netflow_last_file = 'nfcapd.201309131615';//FIXME add last file detection 
$path = $netflow_base_dir. DIRECTORY_SEPARATOR .$netflow_current_dir. DIRECTORY_SEPARATOR .$netflow_last_file;

//Check marks, main cicle
foreach($marks as $mark){
    //Form filter
    $filter = $mark.' and ('.$src.') and not ('.$dst.')';
    //Form nfdump command
    $command = $nfdump.' -r '.$path.' -n '.$num.' -s srcip/packets'.' "'.$filter.'"';
    echo $command."\n";//for debug
    
    $results = shell_exec($command);//exeCute
    var_dump($results);
    //Parse results, return suspects
    //If suspects check dst IPs
    //If many (>5?) report to email
    //TODO
}

//Save suspects

$exec_time = round(microtime(true) - $exec_time,2);
echo "[i] Execution time: $exec_time sec.\n";
?>
