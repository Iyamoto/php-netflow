<?php

/*
 * NetFlow anomaly detector
 * Finds evil activity in LAN
 */
$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

//Get netflow data
$netflow_current_dir = '2013-09-16'; //TODO add date detection
//Detect last modified netflow data file
if ($debug)
    $netflow_last_file = 'nfcapd.201309131735';
else {
    $netflow_last_file = get_lastmodified_file($netflow_base_dir . DIRECTORY_SEPARATOR . $netflow_current_dir);
    echo "[+] Reading data from $netflow_last_file\n";
}
$path = $netflow_base_dir . DIRECTORY_SEPARATOR . $netflow_current_dir . DIRECTORY_SEPARATOR . $netflow_last_file;



//TODO read data from json
//Check marks, main cicle
$suspect_counter = 0;
foreach ($marks as $mark) {
    //Form filter
    echo "[+] Looking for $mark traffic\n";
    $filter = $mark . ' and (' . $lan_src . ') and not (' . $lan_dst . ')';
    //Form nfdump command, sort order: packets
    $command = $nfdump . ' -r ' . $path . ' -n ' . $num . ' -s srcip/packets -o csv' . ' "' . $filter . '"';
    if ($debug) { //gets results from test string, not from nfdump
        $results = $test_results;
        $src_datas = str_to_array($results);
    }
    else
        $src_datas = get_netflow($command); //execute nfdump and parse results to array 

    if (!$src_datas) {
        echo "[-] Suspicious IPs for mark: $mark not found\n";
        continue;
    }
    foreach ($src_datas as $src_data) {
        $src_ip = $src_data[4];
        //echo "[+] Found suspicious IP: $src_ip\n";
        //TODO Check whitelists
        if (isset($whitelist[$mark]))
            if (check_whitelist($src_ip, $whitelist[$mark]))
                continue;
        //Check DST Ips
        $filter = $mark . ' and src ip ' . $src_ip . ' and not (' . $lan_dst . ')';
        $command = $nfdump . ' -r ' . $path . ' -n 100 -s dstip/packets -o csv' . ' "' . $filter . '"';
        if ($debug) {
            $results = $test_results2;
            $dst_datas = str_to_array($results);
        }
        else
            $dst_datas = get_netflow($command);

        if (!$dst_datas) {
            echo "[-] DST IPs for $src_ip not found\n";
            continue;
        }
        $dst_ip_count = sizeof($dst_datas);
        if ($dst_ip_count >= $dst_ip_lvl) {
            echo "[+] Found suspicious IP: $src_ip\n";
            echo "[+] Destination IP stats\n";
            echo "[+] TIME\t\tIP\tPackets\tBytes\n";
            foreach ($dst_datas as $dst_data) {
                $time = $dst_data[0];
                $dst_ip = $dst_data[4];
                $packets = $dst_data[7];
                $bytes = $dst_data[9];
                echo "[+] $time\t$dst_ip\t$packets\t$bytes\n";
                //Form evidence
                $evidences[] = $time . "\t" . $dst_ip . "\t" . $packets . "\t" . $bytes . "\n";
            }
            $one_run[$suspect_counter]['IP'] = $src_ip;
            $one_run[$suspect_counter]['type'] = $mark;
            $one_run[$suspect_counter]['evidences'] = $evidences;
            $suspect_counter++;
            unset($evidences);
            //action($emails, $src_ip, $evidence);//TODO remove
        }
        else
            "[-] Too few DST IPs for $src_ip\n";
    }
}

//Save suspects to json
if (save_json($db_file, $one_run))
    echo "[+] Saved\n";

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>
