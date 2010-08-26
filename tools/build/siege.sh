#!/bin/sh

. include.sh
cd "$BUILD_TOOLS/siege"

$CONFIG = "mage.cfg"

log "Searching for last successful build..."
SB=`wget -q -O - http://guest:@kn.varien.com/teamcity/httpAuth/app/rest/buildTypes/id:bt19/builds/status:SUCCESS/number`

sed -i 's/{{build_name}}/trunk-sample-data/g' "$CONFIG"
sed -i 's/{{build_number}}/$SB/g' "$CONFIG"
sed -i 's/{{db_name}}/$DB_NAME/g' "$CONFIG"
sed -i 's/{{db_user}}/$DB_USER/g' "$CONFIG"
sed -i 's/{{db_pass}}/$DB_PASS/g' "$CONFIG"

log "Started testing..."
./test.py -m run -c "$CONFIG" -o ../../
check_failure $?
log "Preparing report..."
./test.py -m report -c "$CONFIG" -o ../../
check_failure $?

cd $OLDPWD
