#!/bin/sh

OLDPWD=`pwd`
OLDIFS=$IFS
PWD="../"
PHP_BIN="/usr/bin/php"

BUILD_NAME="$1"
BUILD_NUMBER="$2"

BUILD_TYPE_ID="$4"

if [ "$3" = 'oracle' ]
    then
        DB_NAME_PREFIX="b"
    else
        DB_NAME_PREFIX="builds"
fi

# define database name
DB_NAME="${DB_NAME_PREFIX}-${BUILD_NAME}-${BUILD_NUMBER}"
DB_NAME=${DB_NAME//-/_}

# default database user
DB_USER='qa_setup'
DB_PASS='qa_setup'

DB_PREFIX='prfx_'

# default db model
DB_MODEL='mysql4';

# define DB model
if [ "$3" = 'mssql' ]; then
    DB_MODEL='mssql'
    DB_HOST='mssql.kiev-dev'
    DB_PORT='1433'
    DB_SA_USER='sa'
    DB_SA_PASS='123123q'
fi

if [ "$3" = 'oracle' ]; then
    DB_MODEL='oracle'
    DB_HOST='(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=orcl.kiev-dev)(PORT=1521)))(CONNECT_DATA=(SID=MGNTDB)))'
    DB_PORT=''
    DB_SA_USER='MAGENTO'
    DB_SA_PASS='12345'
    DB_USER=$DB_NAME
    DB_PASS=$DB_NAME
fi

if [ "$DB_MODEL" = 'mysql4' ]; then
    DB_HOST='127.0.0.1'
    DB_PORT=3306
    DB_SA_USER='qa_setup'
    DB_SA_PASS='qa_setup'
fi

MAGENTO_FIRSTNAME="John"
MAGENTO_LASTNAME="Lake"
MAGENTO_EMAIL="qa51@varien.com"
MAGENTO_USERNAME="admin"
MAGENTO_PASSWORD="123123q"
MAGENTO_FRONTNAME="control"
ENCRYPTION_KEY="magicdatabasekey"

BUILD_TOOLS=$OLDPWD

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
    if [ "$DB_MODEL" = 'mssql' ]; then
        CH_BASEURL_PWD=`pwd`
        cd $BUILD_TOOLS
        log "Updating base urls ..."
        ${PHP_BIN} -f mmdb/mssql.php -- --shell baseurl --build_name ${BUILD_NAME} --build_number ${1} --db_name ${2} --db_host ${DB_HOST} --db_port ${DB_PORT} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}
        check_failure $?
        cd $CH_BASEURL_PWD
    fi

    if [ "$DB_MODEL" = 'oracle' ]; then
        CH_BASEURL_PWD=`pwd`
        cd $BUILD_TOOLS
        log "Updating base urls ..."
        ${PHP_BIN} -f mmdb/oracle.php -- --shell baseurl --build_name ${BUILD_NAME} --build_number ${1} --db_name ${2} --db_host ${DB_HOST} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}
        check_failure $?
        cd $CH_BASEURL_PWD
    fi

    if [ "$DB_MODEL" = 'mysql4' ]; then
        log "Updating unsecure base url..."
        echo "USE $2; UPDATE ${DB_PREFIX}core_config_data SET value = 'http://$TEAMCITY_BUILDAGENT_DOMAIN/builds/$BUILD_NAME/$1/' WHERE path like 'web/unsecure/base_url';" | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS
        check_failure $?
        log "Updating secure base url..."
        echo "USE $2; UPDATE ${DB_PREFIX}core_config_data SET value = 'https://$TEAMCITY_BUILDAGENT_DOMAIN/builds/$BUILD_NAME/$1/' WHERE path like 'web/secure/base_url';" | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS
        check_failure $?
    fi
}

clean_cache() {
    log "Clearing cache..."
    rm -rf $1/var/cache/*
    check_failure $?
}
