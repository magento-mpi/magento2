#!/bin/bash

. mmdb/include.sh

# Changing current working directory
cd "$PWD"

if [ "$DB_MODEL" = 'mysql4' ]; then
    # Copying local.xml.template for read/write test
    cp -f "$BUILD_TOOLS/local.xml.template" "app/etc/local.xml.template"
    check_failure $?
fi

if [ "$DB_MODEL" = "oracle" ]
    then
        DB_HOSTNAME=''
        DB_DATANAME=$DB_HOST
    else
        DB_DATANAME=$DB_NAME
        if [ "$DB_PORT" = "" ]
            then
                DB_HOSTNAME="$DB_HOST"
            else
                DB_HOSTNAME="${DB_HOST}:${DB_PORT}"
        fi
fi

# Installing build...
$PHP_BIN -f install.php -- --license_agreement_accepted yes \
--locale en_US --timezone "America/Los_Angeles" --default_currency USD \
--db_host "$DB_HOSTNAME" --db_name "$DB_DATANAME"  --db_user "$DB_USER" --db_pass "$DB_PASS" \
--db_prefix "$DB_PREFIX" --db_model "$DB_MODEL" \
--use_rewrites yes \
--admin_frontname "$MAGENTO_FRONTNAME" \
--skip_url_validation yes \
--url "http://$TEAMCITY_BUILDAGENT_DOMAIN/builds/$BUILD_NAME/$BUILD_NUMBER/" \
--secure_base_url "https://$TEAMCITY_BUILDAGENT_DOMAIN/builds/$BUILD_NAME/$BUILD_NUMBER/" \
--use_secure yes --use_secure_admin yes \
--admin_lastname "$MAGENTO_LASTNAME" --admin_firstname "$MAGENTO_FIRSTNAME" --admin_email "$MAGENTO_EMAIL" \
--admin_username "$MAGENTO_USERNAME" --admin_password "$MAGENTO_PASSWORD" \
--encryption_key "$ENCRYPTION_KEY"
check_failure $?

# Changing permission to cache folder as it was created by user which runs install
log "Changing permission for var/cache folder ..."
chmod -R 777 var/cache
check_failure $?

log "Changing permission for media folder ..."
chmod -R 777 media
check_failure $?

if [ "$DB_MODEL" = 'mysql4' ]; then
    # Reverting local.xml.template
    svn revert app/etc/local.xml.template
    check_failure $?
fi

log "Rebuilding indexes ..."
php -f shell/indexer.php -- reindexall
check_failure $?
chmod -fR 777 var/locks

if [ -d "var/debug" ]; then
    chmod -fR 0777 var/debug
fi

cd $OLDPWD
