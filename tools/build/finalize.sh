#!/bin/bash

. include.sh
. take-previous.sh

cd $PWD

ch_baseurl "current" $DB_NAME
clean_cache $BUILD_NUMBER

if [ -L "current" ]; then 
    log "Removing previous 'current' link..."
    rm current
    [ ! "$?" -eq 0 ] && failure
fi
log "Creating 'currect' link..."
ln -sf $BUILD_NUMBER current
[ ! "$?" -eq 0 ] && failure

if [ "$SB" != "" ]; then
    log "Cleaning previous build..."
    ch_baseurl $SB $SB_DB
    clean_cache $SB
fi

log "Saving last successful build flag..."
echo "$BUILD_NUMBER" > "$SUCCESSFUL_BUILDS/$BUILD_NAME"
[ ! "$?" -eq 0 ] && failure

cd $OLDPWD
