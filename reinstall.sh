#!/bin/bash

mysql -u root --password=Mag3nt0! -e "drop database magento2;"
mysql -u root --password=Mag3nt0! -e "create database magento2;"

rm -rf var/*
rm -rf pub/media/*
rm app/etc/local.xml
rm -rf pub/static/adminhtml
rm -rf pub/static/frontend
rm -rf pub/static/install

git checkout -- pub

chmod -R a+wX var
chmod -R a+wX app/etc
chmod -R a+wX pub

php -f dev/shell/install.php -- \
--license_agreement_accepted "yes" \
--locale "en_US" \
--timezone "America/Los_Angeles" \
--default_currency "USD" \
--db_host "127.0.0.1" \
--db_name "magento2" \
--db_user "root" \
--db_pass "Mag3nt0!" \
--url "http://magento.loc/m2/" \
--skip_url_validation \
--use_rewrites "yes" \
--use_secure "no" \
--secure_base_url "https://magento.loc/m2/" \
--use_secure_admin "yes" \
--admin_firstname "AdminFirst" \
--admin_lastname "AdminLast" \
--admin_email "cspruiell@ebay.com" \
--admin_username "admin" \
--admin_password "123123q"
