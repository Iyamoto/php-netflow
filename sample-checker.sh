#!/bin/bash
#rename sample-checker.sh to checker.sh
#chmod +x checker.sh
#replace basedir with your path to php-netflow
#and add to /etc/crontab: */10 * * * * root /path/to/checker.sh >/dev/null 2>&1
basedir="/root/netflow/php-netflow"
archiveindex="/var/www/botolovka/archive/index.html"
cd $basedir
date > lastrun.log
/usr/bin/php checker.php >> lastrun.log
/usr/bin/php filter.php >> lastrun.log
/usr/bin/php reporter.php >> lastrun.log
/usr/bin/php stats.php >> lastrun.log

archivedate=`stat -c%y $archiveindex | gawk '{ print $1 }'`
today=`date +%Y-%m-%d`
if [[ "$archivedate" == "$today" ]]
then echo "[+] No need to acrchive" >> lastrun.log
else /usr/bin/php archive.php >> lastrun.log
fi