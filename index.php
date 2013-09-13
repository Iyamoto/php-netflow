<?php

/*
 * NetFlow anomaly detector
 * Finds evil activity in LAN
 */

mb_internal_encoding("UTF-8");
$exec_time = microtime(true);
require_once 'config.php';
echo "\n[+] Started\n";

//Get netflow data
$netflow_current_dir = '2013-09-13';//TODO add date detection
$netflow_last_file = 'nfcapd.201309131735';//FIXME add last file detection 
$path = $netflow_base_dir. DIRECTORY_SEPARATOR .$netflow_current_dir. DIRECTORY_SEPARATOR .$netflow_last_file;

//TODO read data from json

//Check marks, main cicle
foreach($marks as $mark){
    //Form filter
    $filter = $mark.' and ('.$lan_src.') and not ('.$lan_dst.')';
    //Form nfdump command, sort order: packets
    $command = $nfdump.' -r '.$path.' -n '.$num.' -s srcip/packets -o csv'.' "'.$filter.'"';
    if($debug) { //gets results from test string, not from nfdump
        $results = $test_results;
        $src_datas = str_to_array($results);
    }   else $src_datas = get_netflow($command);//execute nfdump and parse results to array 

    if(!$src_datas) {
        echo "[-] Suspicious IPs for mark: $mark not found\n";
        continue;
    }    
    foreach($src_datas as $src_data){
        $src_ip = $src_data[4];
        echo "[+] Found suspicious IP: $src_ip\n";
        //Check DST Ips
        $filter = $mark.' and src ip '.$src_ip.' and not ('.$lan_dst.')';
        $command = $nfdump.' -r '.$path.' -n 100 -s dstip/packets -o csv'.' "'.$filter.'"';
        if($debug) {
            $results = $test_results2;
            $dst_datas = str_to_array($results);
        }   else $dst_datas = get_netflow($command);
        
        if(!$dst_datas) {
            echo "[-] DST IPs for $src_ip not found\n";
            continue;
        }    
        $dst_ip_count = sizeof($dst_datas);
        if($dst_ip_count>=$dst_ip_lvl){
            echo "[+] Destination IP stats\n";
            echo "[+] TIME\t\tIP\tPackets\tBytes\n";
            $evidence = '';
            foreach($dst_datas as $dst_data){
                $time = $dst_data[0];
                $dst_ip = $dst_data[4];
                $packets = $dst_data[7];
                $bytes = $dst_data[9];
                echo "[+] $time\t$dst_ip\t$packets\t$bytes\n";
                //Form evidence
                $evidence.=$time."\t".$dst_ip."\t".$packets."\t".$bytes."\n";
            }
            action($emails, $src_ip, $evidence);
        } else "[-] Too few DST IPs for $src_ip\n";
    }
   
    break;
}

//TODO Save suspects to json

$exec_time = round(microtime(true) - $exec_time,2);
echo "[i] Execution time: $exec_time sec.\n";

function action($emails, $src_ip, $evidence){
    $subject = "Detected evil IP: $src_ip";
    foreach($emails as $email){
        mail($email,$subject,$evidence);
    }    
}

function get_netflow($command){
    $results = shell_exec($command);//exeCute 
    $data = str_to_array($results);
    return $data;
}

function str_to_array($str){
    $str = trim($str);
    $lines = explode("\r\n",$str);
    if (sizeof($lines)==1) $lines = explode("\n",$str);
    for($i=1;$i<sizeof($lines)-4;$i++){
        $elements[] = explode(',',$lines[$i]); 
    }
    if(sizeof($elements)>0) return $elements;
    else return false;
}
?>
