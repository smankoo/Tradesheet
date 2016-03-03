ssh -i /home/vagrant/bitnami-lamp-keypair.pem bitnami@54.85.151.78 "/opt/bitnami/mysql/bin/mysqldump -uroot -proot trdsheet > /tmp/TRDSHEET_DUMP.sql"
scp -i /home/vagrant/bitnami-lamp-keypair.pem bitnami@54.85.151.78:/tmp/TRDSHEET_DUMP.sql /tmp
ssh -i /home/vagrant/bitnami-lamp-keypair.pem bitnami@54.85.151.78 "rm -f /tmp/TRDSHEET_DUMP.sql"

/usr/bin/mysql -uroot -proot trdsheet < /tmp/TRDSHEET_DUMP.sql
