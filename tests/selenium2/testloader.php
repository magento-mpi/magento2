<?php
define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR', realpath(__DIR__));


function __autoload($className) {
    if (0 === strpos($className, 'Test_')) {
        $className = str_ireplace('Test_', 'tests_', $className);
    }
    if (0 === strpos($className, 'Helper_')) {
        $className = str_ireplace('Helper_', 'bricks_', $className);
    }
    $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $className)));
    $classFile = $classFile . '.php';
    return include $classFile;
}

Core::init();
