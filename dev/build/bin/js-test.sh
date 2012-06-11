#!/bin/bash
OUTPUT_DIR="../../tests/js/test-output"
CONF_FILE="../../tests/js/jsTestDriver.conf"
rm -r $OUTPUT_DIR
mkdir $OUTPUT_DIR

#FIREFOX=`which firefox`
#if [ "$?" -eq 1 ];
#then
#    echo "Firefox not found."
#    exit 1
#fi

#java -jar JsTestDriver.jar --config $CONF_FILE --port 9876 --browser $FIREFOX --tests all --testOutput $OUTPUT_DIR
java -jar JsTestDriver.jar --config $CONF_FILE --port 9876 --browser /usr/bin/firefox --tests all --testOutput $OUTPUT_DIR