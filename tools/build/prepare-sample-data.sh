#!/bin/bash

. include.sh

cd $PWD

log "Retrieving last sample data from magentocommerce.com ..."
wget -q -O sample-data.tar.bz2 http://www.magentocommerce.com/downloads/assets/1.2.0/magento-sample-data-1.2.0.tar.bz2
check_failure $?
tar -xjf sample-data.tar.bz2
log "Preparing media ..."
mv -f magento-sample-data-1.2.0/media/* ./media
check_failure $?
log "Copying DB..."
mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS $DB_NAME < magento-sample-data-1.2.0/magento_sample_data_for_1.2.0.sql
check_failure $?
rm -rf magento-sample-data-1.2.0
check_failure $?

cd $OLDPWD
