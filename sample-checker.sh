#!/bin/bash
#rename sample-checker.sh to checker.sh
#chmod +x checker.sh
#replace basedir with your path to php-netflow
#and add to /etc/crontab: */10 * * * * root /path/to/checker.sh >/dev/null 2>&1
basedir="/root/netflow/php-netflow"
cd $basedir
date > lastrun.log
/usr/bin/php checker.php >> lastrun.log
/usr/bin/php filter.php >> lastrun.log