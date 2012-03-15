#!/bin/sh
php -d zend_extension=`locate xdebug.so` -d xdebug.max_nesting_level=200 `which phpunit` $@
