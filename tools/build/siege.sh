#!/bin/sh

. include.sh
cd "$BUILD_TOOLS/siege"

CONFIG="mage.cfg"

HTML_REPORT="load-test-result.html"

log "Searching for last successful build..."
SB=`wget -q -O - http://guest:@kn.varien.com/teamcity/httpAuth/app/rest/buildTypes/id:bt19/builds/status:SUCCESS/number`

DB_NAME="builds-$BUILD_NAME-$SB"
DB_NAME=${DB_NAME//-/_}

sed -i 's/{{build_name}}/trunk-sample-data/g' "$CONFIG"
sed -i "s/{{build_number}}/$SB/g" "$CONFIG"
sed -i "s/{{db_name}}/$DB_NAME/g" "$CONFIG"
sed -i "s/{{db_user}}/$DB_USER/g" "$CONFIG"
sed -i "s/{{db_pass}}/$DB_PASS/g" "$CONFIG"

#log "Started testing..."
#./test.py -m run -c "$CONFIG" -o ..
#check_failure $?
#log "Preparing report..."
#./test.py -m report -c "$CONFIG" -o ..
#check_failure $?
#cp -R ../artifacts report/logs
cd report
#log "Generating HTML report..."
#php -f console.php -- -a fetch -r artifacts -o $HTML_REPORT
#check_failure $?
#mv $HTML_REPORT ../../artifacts/
#check_failure $?
cd ../../artifacts
touch config.xml
touch checkout.log
touch siege.log
FOLDER=`pwd`
echo "##teamcity[publishArtifacts '$FOLDER/config.xml']"
echo "##teamcity[publishArtifacts '$FOLDER/checkout.log']"
echo "##teamcity[publishArtifacts '$FOLDER/siege.log']"
echo "##teamcity[publishArtifacts '$FOLDER/$HTML_REPORT']"
cd $OLDPWD
