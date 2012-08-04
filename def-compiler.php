<?php

/**
 * Require necessary files
 */
/**
 * Constants definition
 */
define('DS', DIRECTORY_SEPARATOR);
define('BP', __DIR__);


require_once BP . '/lib/Magento/Autoload.php';

$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';
Magento_Autoload::getInstance()->addIncludePath($paths);

use \Zend\Di\Di,
\Zend\Di\Definition\CompilerDefinition;

$array = array();
foreach(glob('var/di/*') as $dir) {
    $array = array_merge($array, require $dir);
}

foreach ($array as $key => $definition) {
    unset($definition['supertypes']);
    $array[$key] = $definition;
}

file_put_contents(
    'var/di/definition.php',
    serialize($array)
);
