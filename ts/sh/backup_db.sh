#!/bin/bash
# backup_db.sh

date
echo "OK, Starting DB Backup"

if [ ! -d /home/bitnami/db_backup ]; then
mkdir /home/bitnami/db_backup
fi

cd /home/bitnami/db_backup

# Compress files older than a week
find . -name "TRDSHT_DB_*.sql" -type f -mtime +7 -exec gzip {} \;

# Delete files older than a month
find . -name "TRDSHT_DB_*.sql" -type f -mtime +30 -exec rm {} \;

OUT_FILE=TRDSHT_DB_`date '+%Y_%m_%d-%H-%M-%S'`.sql

/opt/bitnami/mysql/bin/mysqldump -uroot -proot trdsheet > ${OUT_FILE}

date
echo "Completed backup"
ls -lh ${OUT_FILE}