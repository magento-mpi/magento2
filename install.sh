#!/bin/bash

mkdir -p var/etc
mkdir -p var/cache/layout
mkdir -p var/catalog/category
mkdir -p var/catalog/product
mkdir -p var/session
mkdir -p var/log
chmod 0777 var -R
chmod 0777 www/media -R

echo "Enter your web server domain name:"
read domain

echo "Enter your web server port:"
read port

echo "Enter Magento application base URL path:"
read url

cat app/etc/local.xml.template | sed "s|{DOMAIN}|$domain|g" | sed "s|{PORT}|$port|g" | sed "s|{URL}|$url|g" >app/etc/local.xml
cat www/.htaccess.template | sed "s|{PWD}|`pwd`|g" >www/.htaccess
