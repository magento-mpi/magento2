#!/bin/bash

. include.sh

log "Dropping DB if exists..."
echo "DROP DATABASE IF EXISTS $DB_NAME;" | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS
check_failure $?

log "Creating clean DB..."
echo "CREATE DATABASE $DB_NAME;" | mysql -h $DB_HOST -P $DB_PORT -u$DB_USER -p$DB_PASS
check_failure $?
