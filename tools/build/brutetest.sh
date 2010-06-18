#!/bin/bash

. include.sh

cd $PWD

log "Testing site..."
$BUILD_TOOLS/pavuk.sh "$BUILD_NAME/$BUILD_NUMBER"
check_failure $?
if [ -d $BUILD_NUMBER/var/report/ ] ; then
    find $BUILD_NUMBER/var/report/ -type f && failure
fi

cd $OLDPWD
