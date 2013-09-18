<?php

/*
 * Example config file for PHP-NetFlow
 * Rename to config.php
 * Must be edit variables:
 *  $emails
 *  $netflow_base_dir
 * Tuned for usage with OSSIM
 */

//Install section, edit $emails and profile_id in $netflow_base_dir
$emails[] = 'name@domain.zone'; //Whom to report
$netflow_base_dir = '/var/cache/nfdump/flows/live/profile_id'; //Where to look for netflow data
$nfdump = '/usr/bin/nfdump'; //which nfdump
$tmp_dir = '/tmp/phpnetflow'; //Where to keep phpnetflow data files
$web_dir = '/var/www/bothunter'; //Where to place html reports
$tpl_dir = 'eng-tpl'; //Name of a web template
//End of install section

require_once 'functions.php';

//Build directory structure if needed
if (!is_dir($tmp_dir))
    mkdir($tmp_dir);
if (!is_dir($web_dir)) {
    mkdir($web_dir);
    mkdir($web_dir . DIRECTORY_SEPARATOR . 'archive');
    copy('botolovka' . DIRECTORY_SEPARATOR . 'starter-template.css', $web_dir . DIRECTORY_SEPARATOR . 'starter-template.css');
    recurse_copy('botolovka' . DIRECTORY_SEPARATOR . 'assets', $web_dir . DIRECTORY_SEPARATOR . 'assets');
    recurse_copy('botolovka' . DIRECTORY_SEPARATOR . 'dist', $web_dir . DIRECTORY_SEPARATOR . 'dist');
}

//Init variables
$today = date("Y-m-d");
$db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'onerun.gz';
$daily_db_file = $tmp_dir . DIRECTORY_SEPARATOR . 'daily' . $today . '.gz';

//TODO copy css, js files to $web_dir
//NFDump filters for LAN
$lan_src = 'src net 10.0/8 or src net 192.168/16';
$lan_dst = 'dst net 10.0/8 or dst net 192.168/16 or dst net 169.254/16';

//What are we looking for?
//https://isc.sans.edu/trends.html
$marks[] = 'proto icmp and icmp-type 8';
$marks[] = 'dst port 53';
$marks[] = 'proto tcp and dst port 3389'; //RDP, Virus:Win32/Morto
$marks[] = 'proto tcp and dst port 6667'; //bots looking for C&C?
$marks[] = 'proto tcp and dst port 25'; //Spam bots
$marks[] = 'proto tcp and dst port 445'; //microsoft-ds
$marks[] = 'proto tcp and dst port 139'; //Chode, GodMessageworm, Msinit, netbios-ssn, Netlog, Network, Qaz, Sadmind, SMBRelay
$marks[] = 'proto tcp and dst port 135'; //epmap, loc-srv
$marks[] = 'proto tcp and dst port 9050'; //Tor
$marks[] = 'proto tcp and (dst port 3128 or dst port 8118)'; //Proxy
$marks[] = 'proto tcp and dst port 22'; //ssh scans
$marks[] = 'proto tcp and dst port 23'; //telnet scans
$marks[] = 'proto tcp and dst port 21'; //ftp
$marks[] = 'proto tcp and dst port 5432'; //postgres
$marks[] = 'proto tcp and dst port 7777'; //cbt, FWTK-authsvr, GodMessage, oracle-portal, TheThing(modified), Tini
$marks[] = 'proto tcp and dst port 5555'; //sysbug, personal-agent, rplay, ServeMe
$marks[] = 'proto tcp and dst port 995'; //pop3s
$marks[] = 'proto tcp and dst port 993'; //imaps
$marks[] = 'proto tcp and dst port 5060'; //sip
$marks[] = 'proto tcp and dst port 1234'; //hotline, search-agent, SubSevenJavaclient, UltorsTrojan
$marks[] = 'proto tcp and dst port 3306'; //mysql
$marks[] = 'proto tcp and dst port 1080'; //socks
$marks[] = 'proto tcp and dst port 8080'; //BrownOrifice, Genericbackdoor, http-alt, RemoConChubo, ReverseWWWTunnel, RingZero
$marks[] = 'proto tcp and dst port 1024'; //Jade, kdm, Latinus, NetSpy, RAT
$marks[] = 'proto tcp and dst port 1433'; //ms-sql-s
$marks[] = 'dst port 137'; //Chode, Msinit, netbios-ns, Qaz
$marks[] = 'proto tcp and dst port 1434'; //ms-sql-m
$marks[] = 'proto tcp and dst port 110'; //pop-3, ProMailtrojan
$marks[] = 'proto tcp and dst port 143'; //imap
$marks[] = 'proto tcp and dst port 4444'; //metasploit
$marks[] = 'proto tcp and dst port 42'; //WINS (Host Name Server) 
$marks[] = 'proto tcp and dst port 903'; //NetDevil Backdoor
$marks[] = 'proto tcp and dst port 1025'; //Microsoft Remote Procedure Call (RPC) service and Windows Messenger port
$marks[] = 'proto tcp and dst port 2745'; //backdoor of Bagle worm
$marks[] = 'proto tcp and dst port 3127'; //backdoor of MyDoom worm
$marks[] = 'proto tcp and dst port 5000'; //upnp (Universal Plug and Play: MS01-059)
$marks[] = 'proto tcp and dst port 6129'; //dameware (Dameware Remote Admin)

$whitelist['proto icmp and icmp-type 8'][] = '10.10.10.1'; //White IP for first mark
$whitelist['dst port 53'][] = '10.10.10.2'; //White IP for second mark
//TODO add netmask support for the whitelist

$num = 10; //Define number of top N for ntpdump
$dst_ip_lvl = 2; //Action and report lvl for dst IPs

$debug = false;

$test_results = '';
$test_results2 = '';
?>
