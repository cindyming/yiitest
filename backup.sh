#!bin/bash

cd /home/backup

echo "You are In Backup Directory"

Now=$(date +"%d_%m_%Y_%H_%M_%S")

File=backup_$Now.sql.gz

mysqldump -uroot  -palks@111 mgjiayuan --add-drop-table | gzip > $File

mysql -uroot -palks@111 mgjiayuan -e "insert into backup (filename) values (\"$File\");"

echo "Your Database Backup Successfully Completed"
