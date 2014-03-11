#!/bin/bash
#
# {license_notice}
#
# @category    Magento
# @package     Magento
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#

baseStructure=( 'config' 'fixture' 'framework' 'testsuite' 'var' )

OS=`uname -s`
if [ "$OS" = "Darwin" ]
then
	# We don't have `readlink -f` on OS X, so we roll our own, courtesy of:
    #   http://stackoverflow.com/questions/1055671/how-can-i-get-the-behavior-of-gnus-readlink-f-on-a-mac
    cd `dirname $0`
    TARGET_FILE=`basename $0`
    # Iterate down a (possible) chain of symlinks

    while [ -L "$TARGET_FILE" ]
    do
        TARGET_FILE=`readlink $TARGET_FILE`
            cd `dirname $TARGET_FILE`
            TARGET_FILE=`basename $TARGET_FILE`
    done

    # Compute the canonicalized name by finding the physical path
    # for the directory we're in and appending the target file.

    ABSPATH=`pwd -P`
else
    ABSPATH=`dirname $(readlink -f $0)`
fi

function parseArgs()
{
	IFS=','
	configs=$ARGS
	# Defining arguments passed to script in command line
	let i=0
	# Just showing configurations that will be used in tests (marked up as "application:browser")
	for config in $configs
	do
		case "$config" in
		*:*)
		conf=`echo "$config" | sed 's/ //g' | sed 's/:/_/g'`
		resultArr[$i]=$conf
		((i++))
		;;
		*)
		esac
	done
}

function startPreparation()
{
    for (( i=0; i<${#resultArr[@]}; i++))
    do
        `mkdir -p $date/${resultArr[${i}]}_$i && cp *.* $date/${resultArr[${i}]}_$i && cp -R ${baseStructure[@]} $date/${resultArr[${i}]}_$i`
        CONFIG=$ABSPATH/$date/${resultArr[${i}]}_$i
        IFS="_"
        appbro=(${resultArr[${i}]})
        IFS=','
        if [ -n "${appbro[2]}" ]
        then
            if [ -e "$CONFIG/${appbro[2]}" ]
            then
                `cp $CONFIG/${appbro[2]} $CONFIG/phpunit.xml`
            fi
        fi

        if [ -e "$CONFIG/phpunit.xml" ]
	    then
	        phpunitArr[$i]="$CONFIG"
		    awk "BEGIN { a[0]=\"default: *${appbro[1]} #\"; a[1]=\"default: *${appbro[0]} #\"; } /default: */ { gsub( \"default: *\", a[i++]); i%=2 }; 1" $CONFIG/config/config.yml > TMP
		    cat TMP > $CONFIG/config/config.yml
	    else
    		echo "Error: Config file 'phpunit.xml' in $CONFIG doesn't exist."
    		exit 1
    	fi
    done
}

function runTest()
{
	for (( i=0; i<${#phpunitArr[@]}; i++))
	do
		eval "cd ${phpunitArr[${i}]}"
		eval exec "/usr/bin/phpunit -c ${phpunitArr[${i}]}/phpunit.xml &"
		pid=$!
		echo "PID is: $pid"
	done
}

echo "********************************************************************************"
echo "You can pass parameters to the script in case you would like to run \
 several configurations and browsers at the same time."
echo "For passing parameters use next template: \
 'runtests.sh application:browser, application:browser'"
 echo "You can add separate phpunit.xml for each configuration: \
  'runtests.sh application:browser:phpunit1.xml, application:browser:phpunit2.xml'"
echo "Where application is name of link to default application \
 (by default: *mage)"
echo "And browser is name of link to default browser (by default: *firefox)"
echo "Customized phpunit.xml (phpunit1.xml, phpunit2.xml, etc) should be created in the same\
 directory with original phpunit.xml"
echo "Do NOT use '*' in passing parameters."
echo "Example: 'runtests.sh mage:googlechrome:phpunit1.xml, enterprise:firefox, mage:firefox' \
 will execute 3 instances of tests at the same time."
echo "For each run of the script new folder inside the PWD will be created."
echo "********************************************************************************"

if [ "$*" ]
then
   	ARGS=$*;
   	parseArgs
	for (( i=0; i<${#baseStructure[@]}; i++))
	do
       	if [ ! -d ${baseStructure[${i}]} ]
        then
   	        echo "Folder ${baseStructure[${i}]} is not found."
           	exit 1
        fi
	done
	date=$(date +"%s")
	startPreparation
	runTest ${phpunitArr[@]}
else
	CONFIG=$ABSPATH/phpunit.xml

	if [ -e $CONFIG ]
	then
		phpunit --configuration $CONFIG
	else
		echo "Error: The file $CONFIG doesn't exist."
		exit 1
	fi
fi
