#!/bin/bash
basedir="/root/netflow/php-netflow"
cd $basedir
date > lastrun.log
/usr/bin/php checker.php >> lastrun.log
/usr/bin/php filter.php >> lastrun.log