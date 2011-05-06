#!/bin/bash

. include.sh

cd $PWD/../

. $BUILD_TOOLS/take-previous.sh

if [ "$SB" == "" ]; then
    exit
fi

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

echo 'SHOW DATABASES;' | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS | grep $SB_DB > /dev/null
if [ "$?" -eq 0 ] ; then
    log "Copying DB..."
    mysqldump -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS $SB_DB | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS $DB_NAME
    check_failure $?
    ch_baseurl $BUILD_NUMBER $DB_NAME
fi

cd $OLDPWD
