<?php

/*
 * Filter one run activity to daily data
 */
$exec_time = microtime(true);
require_once 'config.php';
require_once 'functions.php';
echo "\n[+] Started\n";

//Read one_run
$one_run = read_db_from_file($db_file);
if ($one_run) {
    $one_run_size = sizeof($one_run);
    echo "[+] Read $one_run_size blocks from $db_file\n";
}
else
    exit('Problem with one_run file');

//Read daily data
$daily = read_db_from_file($daily_db_file);
if ($daily) { //Daily db exists
    $daily_size = sizeof($daily);
    echo "[+] Read $daily_size daily blocks\n";
    //Add to daily
    foreach ($one_run as $one_run_block) {
        $is_ip_found = false;
        foreach ($daily as $daily_block_ip => $daily_block_types) {
            if ($one_run_block['IP'] == $daily_block_ip) {//IP found
                echo "[i] $daily_block_ip found\n";
                $is_type_found = false;
                foreach ($daily_block_types as $daily_block_type => $daily_block_evidences) {
                    if ($one_run_block['type'] == $daily_block_type) {//Traffic type found
                        foreach ($one_run_block['evidences'] as $evidence) {
                            $daily[$one_run_block['IP']][$one_run_block['type']][] = $evidence;
                        }
                        $is_type_found = true;
                        break;
                    }
                }
                //Traffic type not found
                if (!$is_type_found) {
                    $tmp_type = $one_run_block['type'];
                    echo "[i] Traffic type $tmp_type not found\n";
                    $daily[$one_run_block['IP']][$one_run_block['type']] = $one_run_block['evidences'];
                }
                $is_ip_found = true;
                break; //go to next ip
            }
        }
        //IP not found
        if (!$is_ip_found) {
            $tmp_ip = $one_run_block['IP'];
            echo "[i] IP $tmp_ip not found\n";
            $daily[$one_run_block['IP']][$one_run_block['type']] = $one_run_block['evidences'];
            //time for some action
            if (!$debug) {
                $mail_results = action($emails, $one_run_block['IP'], $one_run_block['type'], $one_run_block['evidences']);
                if ($mail_results)
                    echo "[+] Mail sent\n";
                else
                    var_dump($mail_results);
            }
        }
    }
} else { //Daily db is empty
    unset($daily);
    //Form daily db
    $daily_counter = 0;
    foreach ($one_run as $block) {
        $daily[$block['IP']][$block['type']] = $block['evidences'];
        $daily_counter++;
        if (!$debug) {
            $mail_results = action($emails, $block['IP'], $block['type'], $block['evidences']);
            if ($mail_results)
                echo "[+] Mail sent\n";
            else
                var_dump($mail_results);
        }
    }
}

//Save daily data to json
if (save_json($daily_db_file, $daily))
    echo "[+] Saved\n";

$exec_time = round(microtime(true) - $exec_time, 2);
echo "[i] Execution time: $exec_time sec.\n";
?>