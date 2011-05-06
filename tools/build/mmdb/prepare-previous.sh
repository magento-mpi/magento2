#!/bin/bash

. include.sh

cd $PWD/../../

. $BUILD_TOOLS/../take-previous.sh

if [ -d "$SB/websites" ]; then
    log "Copying websites..."
    cp -af "$SB/websites" ./$BUILD_NUMBER
    check_failure $?
fi

if [ -d "$SB/media" ]; then
    log "Copying media..."
    cp -af "$SB/media" ./$BUILD_NUMBER
    check_failure $?
fi

case $DB_MODEL in
    mssql)
        failed "Database model '$DB_MODEL' not supported by this build scripts."
        ;;
    mysql4)
        echo 'SHOW DATABASES;' | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS | grep $SB_DB > /dev/null
        if [ "$?" -eq 0 ] ; then
            log "Copying DB..."
            mysqldump -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS $SB_DB | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS $DB_NAME
            check_failure $?
            ch_baseurl $BUILD_NUMBER $DB_NAME
        fi
        ;;
    oracle)
        failed "Database model '$DB_MODEL' not supported by this build scripts."
        ;;
    *)
        failed "Database model '$DB_MODEL' not supported by this build scripts."
        ;;
esac



if [ "$DB_MODEL" = 'mssql' ]; then
    ${PHP_BIN} -f mmdb/mssql.php -- --shell prepare --db_name ${DB_NAME} --db_host ${DB_HOST} --db_port ${DB_PORT} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}
    check_failure $?
fi

if [ "$DB_MODEL" = 'oracle' ]; then
    ${PHP_BIN} -f mmdb/oracle.php -- --shell prepare --db_name ${DB_NAME} --db_host ${DB_HOST} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}
    check_failure $?
fi

cd $OLDPWD
