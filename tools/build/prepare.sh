#!/bin/bash

. include.sh

log "Dropping DB if exists..."
echo "DROP DATABASE IF EXISTS $DB_NAME;" | mysql -u root
check_failure $?

log "Creating clean DB..."
echo "CREATE DATABASE $DB_NAME;" | mysql -u root
check_failure $?
