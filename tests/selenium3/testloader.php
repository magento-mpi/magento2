<?php

define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', realpath(__DIR__));
define('LIB_DIR', realpath(BASE_DIR . '/lib'));

// Ensure lib is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIB_DIR),
    get_include_path(),
)));

require_once 'Autoloader.php';
Autoloader::init();
Core::init();
