#!/bin/bash

date
echo "OK, starting now..."

cd /home/bitnami/cibc-box

sftp -b /dev/stdin  <source_ftp_server> <<EOF
get TRDSHT*
exit
EOF
x=$?

if [ $? -ne 0 ]; then
	echo "SFTP Unsuccessful"
	exit $x
else
	echo "SFTP Successful"
	
	# Compress files older than a week
	find . -name "TRDSHT_*.csv" -type f -mtime +7 -exec gzip {} \;
	
	# Delete files older than a month
	find . -name "TRDSHT_*.csv" -type f -mtime +30 -exec rm {} \;

	NEWFILE=`ls -1 TRDSHT_*.csv | grep -v LATEST | sort -n | tail -1`
	if [ -f ${NEWFILE} ]; then
		rm TRDSHT_Asset_And_Accrual_Detail_LATEST.csv
		ln -s ${NEWFILE} TRDSHT_Asset_And_Accrual_Detail_LATEST.csv
	fi
fi
