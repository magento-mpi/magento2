#!/bin/sh

. mmdb/include.sh

log "Searching for last successful build..."
SB=`wget -q -O - http://rest:gyroscope@kn.varien.com/teamcity/httpAuth/app/rest/buildTypes/id:$4/builds/status:SUCCESS/number`
if [ -d $SB ]; then
    SB_DB_TEMP="builds-$BUILD_NAME-$SB"
    SB_DB=${SB_DB_TEMP//-/_}
else
    SB_DB=""
fi

cd $PWD/../

if [ -L "current" ]; then
    log "Removing previous 'current' link..."
    rm current
    check_failure $?
fi
log "Creating 'currect' link..."
ln -sf $BUILD_NUMBER current
check_failure $?

ch_baseurl "current" $DB_NAME
clean_cache $BUILD_NUMBER

if [ "$SB" != "" ]; then
    log "Cleaning previous build..."
    ch_baseurl $SB $SB_DB
    clean_cache $SB
fi

cd $OLDPWD
