#!/bin/bash

. include.sh

cd $PWD/../

. $BUILD_TOOLS/take-previous.sh

ch_baseurl "current" $DB_NAME
clean_cache $BUILD_NUMBER

if [ -L "current" ]; then
    log "Removing previous 'current' link..."
    rm current
    check_failure $?
fi
log "Creating 'currect' link..."
ln -sf $BUILD_NUMBER current
check_failure $?

if [ "$SB" != "" ]; then
    log "Cleaning previous build..."
    ch_baseurl $SB $SB_DB
    clean_cache $SB
fi

cd $OLDPWD
