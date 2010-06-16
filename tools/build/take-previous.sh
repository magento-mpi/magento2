#!/bin/bash

log "Searching for last successful build..."
if [ -f  $SUCCESSFUL_BUILDS/$BUILD_NAME ]; then
    SB=`cat $SUCCESSFUL_BUILDS/$BUILD_NAME`
    [ ! "$?" -eq 0 ] && failure
    log "Searching for DB..."
    SB_DB_TEMP="builds-$BUILD_NAME-$SB"
    SB_DB=${SB_DB_TEMP//-/_}
    echo 'SHOW DATABASES;' | mysql -u root | grep $SB_DB > /dev/null
    [ ! "$?" -eq 0 ] && failure
else
    log "Not found"
fi

