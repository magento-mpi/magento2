#!/bin/sh

OLDPWD=`pwd`
PHP_BIN="/usr/bin/php"
PWD="../"
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

DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_USER="qa_setup"
DB_PASS="qa_setup"
DB_PREFIX="prefix_"

BUILD_TOOLS="$OLDPWD"

DB_NAME="builds-$BUILD_NAME-$BUILD_NUMBER"
DB_NAME=${DB_NAME//-/_}
DB_NAME=${DB_NAME//./_}
SB=""

NUM_BUILDS=10

check_failure() {
    if [ "${1}" -ne "0" ]; then
        cd $OLDPWD
        IFS=$OLDIFS
        failed "ERROR # ${1} : ${2}"
    fi
}

failed() {
   log "$1"
   exit 1
}

log() {
    echo "$1"
}

ch_baseurl() {
    log "Updating unsecure base url..."
    echo "USE $2; UPDATE prefix_core_config_data SET value = 'http://$TEAMCITY_BUILDAGENT_DOMAIN/builds/$BUILD_NAME/$1/' WHERE path like 'web/unsecure/base_url';" | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS
    check_failure $?
    log "Updating secure base url..."
    echo "USE $2; UPDATE prefix_core_config_data SET value = 'https://$TEAMCITY_BUILDAGENT_DOMAIN/builds/$BUILD_NAME/$1/' WHERE path like 'web/secure/base_url';" | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS
    check_failure $?
}

clean_cache() {
    log "Clearing cache..."
    rm -rf $1/var/cache/*
    check_failure $?
}

if [ "$TEAMCITY_BUILDAGENT_DOMAIN" == "" ]; then
    failed "Teamcity Build Agent configured incorectly. Please define its domain name through build agent configuration!"
fi
