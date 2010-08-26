#!/bin/bash

log "Searching for last successful build..."
SB=`wget -q -O - http://guest:@kn.varien.com/teamcity/httpAuth/app/rest/buildTypes/id:$3/builds/status:SUCCESS/number`
if [ -d $SB ]; then
    log "Searching for DB..."
    SB_DB_TEMP="builds-$BUILD_NAME-$SB"
    SB_DB=${SB_DB_TEMP//-/_}
    echo 'SHOW DATABASES;' | mysql -u root | grep $SB_DB > /dev/null
    check_failure $?
else
    log "Not found"
    exit 1
fi

