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
foreach(glob('app/code/core/Mage/*') as $dir) {
    $compiler = new CompilerDefinition();
    $compiler->addDirectory($dir);

    $controllerPath = $dir . '/controllers/';
    if (file_exists($controllerPath)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath)) as $file) {
            if (! $file->isDir()) {
                require_once $file->getPathname();
            }
        }
    }


    $compiler->compile();
    $array = array_merge($array, $compiler->toArrayDefinition()->toArray());
}

file_put_contents(
    'var/di/definition.php',
    '<?php return ' . var_export($array, true) . ';'
);
