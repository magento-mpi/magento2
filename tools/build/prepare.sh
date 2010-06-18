#!/bin/bash

. include.sh

cd $PWD

if [ ! -d "$BUILD_NUMBER/websites" ] ; then
    log "Creating websites folder..."
    mkdir "$BUILD_NUMBER/websites"
    check_failure $? 
fi

log "Changing permissions for websites folder..."
chmod -R 777 "$BUILD_NUMBER/websites"
check_failure $?

log "Changing permissions for media folder..."
chmod -R 777 "$BUILD_NUMBER/media"
check_failure $?	

log "Changing permissions for var folder..."
chmod -R 777 "$BUILD_NUMBER/var/"
check_failure $?	

log "Changing permissions for app/etc folder..."
chmod -R 777 "$BUILD_NUMBER/app/etc"
check_failure $?

log "Dropping DB if exists..."
echo "DROP DATABASE IF EXISTS $DB_NAME;" | mysql -u root
check_failure $?

log "Creating clean DB..."
echo "CREATE DATABASE $DB_NAME;" | mysql -u root
check_failure $?

cd $OLDPWD
