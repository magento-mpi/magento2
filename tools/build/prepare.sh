#!/bin/bash

. include.sh

cd $PWD

if [ ! -d "$BUILD_NUMBER/websites" ] ; then
    log "Creating websites folder..."
    mkdir "$BUILD_NUMBER/websites"
    [ ! "$?" -eq 0 ] && failure
fi

log "Changing permissions for websites folder..."
chmod -R 777 "$BUILD_NUMBER/websites"
[ ! "$?" -eq 0 ] && failure

log "Changing permissions for media folder..."
chmod -R 777 "$BUILD_NUMBER/media"
[ ! "$?" -eq 0 ] && failure	

log "Changing permissions for var folder..."
chmod -R 777 "$BUILD_NUMBER/var/"
[ ! "$?" -eq 0 ] && failure	

log "Changing permissions for app/etc folder..."
chmod -R 777 "$BUILD_NUMBER/app/etc"
[ ! "$?" -eq 0 ] && failure

log "Dropping DB if exists..."
echo "DROP DATABASE IF EXISTS $DB_NAME;" | mysql -u root
[ ! "$?" -eq 0 ] && failure

log "Creating clean DB..."
echo "CREATE DATABASE $DB_NAME;" | mysql -u root
[ ! "$?" -eq 0 ] && failure

cd $OLDPWD
