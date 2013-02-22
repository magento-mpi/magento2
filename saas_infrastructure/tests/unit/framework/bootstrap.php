<?php
/**
 * Autoload for unit tests
 */
spl_autoload_register(function($class) {
    $file = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class) . '.php';
    include $file;
});
