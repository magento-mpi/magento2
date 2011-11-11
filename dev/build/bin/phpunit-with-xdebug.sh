#!/bin/sh
php -d zend_extension=`locate xdebug.so` `which phpunit` $@
