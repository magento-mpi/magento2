#!/bin/bash
. include.sh

cd $PWD

log "Testing site..."
$BUILD_TOOLS/pavuk -quiet -nredirs 10 -retry 5 -mode dontstore --logfile /dev/null -lmax 5 -dmax 50 -remove_adv -skip_pattern *.jpg,*.gif,*.css,*.ico,*.png -noRobots -adomain kq.varien.com http://kq.varien.com/builds/$BUILD_NAME/$BUILD_NUMBER
check_failure $?

if [ -d $BUILD_NUMBER/var/report/ ] ; then
    find $BUILD_NUMBER/var/report/ -type f && failed "Please check report folder for more info..."
fi

cd $OLDPWD
