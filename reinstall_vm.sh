#!/bin/bash


DB_USER='magetwo'
DB_PASS='magetwo'
DB='magetwo'
ROOT='/var/mage/magetwo'
HOST='magetwo.my'
mysql -u$DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB; create database $DB;"
cd $ROOT



echo 'start installation...'
rm -rf var/*
cp app/etc/enterprise/module.xml.dist app/etc/enterprise/module.xml
rm -f app/etc/local.xml
php -f dev/shell/install.php -- --license_agreement_accepted yes --locale en_US --timezone "America/Los_Angeles" --default_currency USD --db_host localhost --db_name $DB --db_user $DB_USER --db_pass $DB_PASS --url "http://$HOST/" --use_rewrites yes --use_secure yes --secure_base_url "http://$HOST/" --use_secure_admin yes --admin_lastname Owner --admin_firstname Store --admin_email "admin@example.com" --admin_username admin --admin_password 123123q --encryption_key "EncryptionKey"
mysql -u$DB_USER -p$DB_PASS -e "INSERT INTO $DB.core_config_data(path, value) VALUES('admin/security/use_form_key', 0);"
mysql -u$DB_USER -p$DB_PASS -e "INSERT INTO $DB.core_config_data(path, value) VALUES('admin/security/session_lifetime', 86400);"
chown -R magento.magento ./*
