#!/bin/bash

log "Searching for last successful build..."
SB=`wget -q -O - http://rest:gyroscope@kn.varien.com/teamcity/httpAuth/app/rest/buildTypes/id:$BUILD_TYPE_ID/builds/status:SUCCESS/number`
if [[ -n $SB && -d $SB ]]; then
    log "Searching for DB..."
    SB_DB_TEMP="builds-$BUILD_NAME-$SB"
    SB_DB=${SB_DB_TEMP//-/_}
    echo 'SHOW DATABASES;' | mysql -u root | grep $SB_DB > /dev/null
    check_failure $?
else
    log "Not found"
    SB=""
fi
