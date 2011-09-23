<?php

$includePath = array(
    __DIR__,
    dirname(__DIR__) . '/testsuite',
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $includePath));

spl_autoload_register(function ($class) {
    $file = str_replace('_', '/', $class) . '.php';
    require_once $file;
});
