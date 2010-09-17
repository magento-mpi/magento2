<?php

define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', realpath(dirname(__FILE__)));
define('LIB_DIR', realpath(BASE_DIR . '/lib'));

// Ensure lib is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_DIR),
    get_include_path(),
)));

require_once 'lib/Autoloader.php';
Autoloader::init();
Arguments::init();
Core::init();
TestStarter::run();

