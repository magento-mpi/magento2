#!/bin/sh

. mmdb/include.sh

if [ "$DB_MODEL" = 'mssql' ]; then
    ${PHP_BIN} -f mmdb/mssql.php -- --shell prepare --db_name ${DB_NAME} --db_host ${DB_HOST} --db_port ${DB_PORT} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}
    check_failure $?
fi

if [ "$DB_MODEL" = 'oracle' ]; then
    echo "${PHP_BIN} -f mmdb/oracle.php -- --shell prepare --db_name ${DB_NAME} --db_host ${DB_HOST} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}"
    ${PHP_BIN} -f mmdb/oracle.php -- --shell prepare --db_name ${DB_NAME} --db_host ${DB_HOST} --db_user ${DB_SA_USER} --db_pass ${DB_SA_PASS}
    check_failure $?
fi

if [ "$DB_MODEL" = 'mysql4' ]; then
    # drop database if exists
    log "Dropping DB if exists ..."
    echo "DROP DATABASE IF EXISTS ${DB_NAME};" | mysql -h ${DB_HOST} -P ${DB_PORT} -u${DB_SA_USER} -p${DB_SA_PASS}
    check_failure $?

    # create database
    log "Creating clean DB ..."
    echo "CREATE DATABASE ${DB_NAME};" | mysql -h ${DB_HOST} -P ${DB_PORT} -u${DB_USER} -p${DB_PASS}
    check_failure $?
fi
