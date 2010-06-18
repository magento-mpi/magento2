#!/bin/sh
OLDPWD=`pwd`
PHP_BIN="/usr/bin/php"
PWD="/opt/builds/$1/"
OLDIFS=$IFS

BUILD_NAME="$1"
BUILD_NUMBER="$2"
MAGENTO_FIRSTNAME="John"
MAGENTO_LASTNAME="Lake"
MAGENTO_EMAIL="qa51@varien.com"
MAGENTO_USERNAME="admin"
MAGENTO_PASSWORD="123123q"
MAGENTO_FRONTNAME="control"
ENCRYPTION_KEY="mega1nightly1test1build"

DB_HOST="127.0.0.1:3306"
DB_USER="qa_setup"
DB_PASS="qa_setup"
DB_PREFIX="prefix_"

BUILD_TOOLS="/opt/builds/build"
SUCCESSFUL_BUILDS="/opt/builds/build/successful"
LOGS="/opt/builds/build/logs"

DB_PRE_NAME="builds-$BUILD_NAME-$BUILD_NUMBER"
DB_NAME=${DB_PRE_NAME//-/_}

SB=""

NUM_BUILDS=10

check_failure () {
    if [ "${1}" -ne "0" ]; then
        echo "ERROR # ${1} : ${2}"
        cd $OLDPWD
        IFS=$OLDIFS
        exit 1
    fi  
}

log() {
    echo "$1"
}

ch_baseurl() {
    log "Updating unsecure base url..."
    echo "USE $2; UPDATE prefix_core_config_data SET value = 'http://kq.varien.com/builds/$BUILD_NAME/$1/' WHERE path like 'web/unsecure/base_url';" | mysql -u root
    check_failure $?
    log "Updating secure base url..."
    echo "USE $2; UPDATE prefix_core_config_data SET value = 'https://kq.varien.com/builds/$BUILD_NAME/$1/' WHERE path like 'web/secure/base_url';" | mysql -u root
    check_failure $?
}

clean_cache() {
    log "Clearing cache..."
    rm -rf $1/var/cache/*
    check_failure $?
}

if [ ! -d "$SUCCESSFUL_BUILDS" ] ; then
    log "Creating folder for flags..."
    mkdir "$SUCCESSFUL_BUILDS"
    check_failure $?
fi

if [ ! -d "$LOGS" ] ; then
    log "Creating folder for logs..."
    mkdir "$LOGS"
    check_failure $?
fi
