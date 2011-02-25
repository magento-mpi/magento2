#!/bin/bash
. include.sh

cd $PWD

log "Testing site..."
chmod +x $BUILD_TOOLS/pavuk
$BUILD_TOOLS/pavuk -quiet -nredirs 10 -retry 5 -mode dontstore --logfile /dev/null -lmax 5 -dmax 50 -remove_adv -skip_pattern *.jpg,*.gif,*.css,*.ico,*.png -noRobots -adomain kq.varien.com http://kq.varien.com/builds/$BUILD_NAME/$BUILD_NUMBER
#we should ignore this bc it can be 503, 404, etc and we need failure only if report page present.
#check_failure $?

if [ -d var/report/ ] ; then
    find var/report/ -type f && failed "Please check report folder for more info..."
fi

cd $OLDPWD
