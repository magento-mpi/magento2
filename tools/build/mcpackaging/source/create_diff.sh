#!/bin/sh -x

if test "x$1" = "x" || test "x$2" = "x"; then
 echo 'Please enter two dirs to make diff'
 exit;
fi

DIR_FIRST=$1
DIR_SECOND=$2

rm -rf $DIR_FIRST/var/cache
rm -rf $DIR_FIRST/var/session

rm -rf $DIR_SECOND/var/cache
rm -rf $DIR_SECOND/var/session

diff -q -r --exclude=".svn" $DIR_FIRST $DIR_SECOND > diff.log

php -f check_diff.php > diff_log.txt

#more diff_log.txt| grep -v .svn > diff_log.txt