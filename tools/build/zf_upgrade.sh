#!/bin/sh

check_failure() {
    if [ "${1}" -ne "0" ]; then
        echo "Failed\n"
        echo "ERROR # ${1} : ${2}"
        exit 1
    else
        echo "OK"
    fi
}

if [ ! $1 ];
then
echo "Script usage: ./zf_upgrade.sh ZFVERSION"
echo "Example: ./zf_upgrade.sh 1.11.1"
exit 1
fi

ZF="ZendFramework-$1-minimal"
ZF_ARCH="$ZF.zip"
LINK="http://framework.zend.com/releases/ZendFramework-$1/$ZF_ARCH"
TMP_LIB="lib_new_zf"
OLD_ZF_DIR="lib/Zend_old"
ZF_DIR="lib/Zend"
cd ../../

echo "Updating checkout..."
svn up --quiet
check_failure $?

#removing temp directory
rm -rf $TMP_LIB

if [ ! -f $ZF_ARCH ];
then
    echo "Downloading ZF from web server..."
    wget -q $LINK
    check_failure $?
else
    echo "Using local archive."
fi

echo "Renaming old version..."
mv $ZF_DIR $OLD_ZF_DIR
check_failure $?

echo "Unpacking new version..."
unzip -qo -d $TMP_LIB $ZF_ARCH "*library*"
check_failure $?

echo "Copying new version to $ZF_DIR..."
cp -r "$TMP_LIB/$ZF/library/Zend" $ZF_DIR
check_failure $?

echo "Calculating difference..."
DEPFILES=`diff -rq $ZF_DIR $OLD_ZF_DIR | grep -v "Files " | grep -v "\.svn" | grep -v "replace_recursive.php" | grep "Only in $OLD_ZF_DIR" | awk '{print $3$4}' | tr : /`

echo "Fixing svn structure..."
ZFSVN=`svn info $OLD_ZF_DIR | grep "URL" | awk '{print $2}'`
svn co --quiet --force "$ZFSVN" $ZF_DIR
check_failure $?

echo "Adding new files..."
svn st $ZF_DIR | grep ^? | awk '{print "svn add "$2}' | bash

echo "Removing deprecated files..."
for f in $DEPFILES
do
	svn rm $f
done

echo "Fixing files..."
cd $ZF_DIR
php -f replace_recursive.php
cd ../../

echo "Cleaning up ..."
rm -rf $TMP_LIB
rm $ZF_ARCH
rm -rf $OLD_ZF_DIR

echo "Now you can commit changes."
echo "Check svn log for fixes in ZF performed by Core Team. Reapply such if they are still not fixed in ZF."
