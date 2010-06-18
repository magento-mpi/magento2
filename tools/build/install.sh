#!/bin/bash

. include.sh

# Changin current working directory
cd "$PWD"

# Copying local.xml.template
cp -f "$BUILD_TOOLS/local.xml.template" "$BUILD_NUMBER/app/etc/local.xml.template"
check_failure $?

# Installing build...
$PHP_BIN -f $BUILD_NUMBER/install.php -- --license_agreement_accepted yes \
--locale en_US --timezone "America/Los_Angeles" --default_currency USD \
--db_host "$DB_HOST" --db_name "$DB_NAME"  --db_user "$DB_USER" --db_pass "$DB_PASS" \
--db_prefix "$DB_PREFIX" \
--use_rewrites yes \
--admin_frontname "$MAGENTO_FRONTNAME" \
--skip_url_validation yes \
--url "http://kq.varien.com/builds/$BUILD_NAME/$BUILD_NUMBER/" \
--secure_base_url "https://kq.varien.com/builds/$BUILD_NAME/$BUILD_NUMBER/" \
--use_secure yes --use_secure_admin yes \
--admin_lastname "$MAGENTO_LASTNAME" --admin_firstname "$MAGENTO_FIRSTNAME" --admin_email "$MAGENTO_EMAIL" \
--admin_username "$MAGENTO_USERNAME" --admin_password "$MAGENTO_PASSWORD" \
--encryption_key "$ENCRYPTION_KEY" 
check_failure $?

# Reverting local.xml.template 
svn revert $BUILD_NUMBER/app/etc/local.xml.template
check_failure $?

cd $OLDPWD
