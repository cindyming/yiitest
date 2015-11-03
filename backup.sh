#!bin/bash

cd /home/backup

echo "You are In Backup Directory"

Now=$(date +"%d-%m-%Y--%H:%M:%S")

File=backup-$Now.sql.gz

mysqldump -uroot  -palks@111 mgjiayuan --add-drop-table | gzip > $File

mysql -uroot -palks@111 mgjiayuan -e "insert into backup (filename) values (\"$File\");"

echo "Your Database Backup Successfully Completed"
