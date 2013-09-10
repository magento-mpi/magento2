<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* Identify class list files */
if (isset($argv[1]) && realpath($argv[1])) {
    $path = realpath($argv[1]);
} else {
    $path = __DIR__ . '/log';
}

if (is_dir($path)) {
    $files = glob($path . '/*.ser');
} else {
    $files = array($path);
}

/* Load class names array */
$classes = array();
foreach ($files as $file) {
    $fileClasses = unserialize(file_get_contents($file));
    $classes = array_merge($classes, $fileClasses);
}

sort($classes);
$baseDir = realpath(__DIR__ . '/../../../../../') . DIRECTORY_SEPARATOR;
$sources = array('app/code', 'lib',);

$map = array();
foreach ($classes as $class) {
    $file = '/' . str_replace('_', '/', $class) . '.php';
    foreach ($sources as $folder) {
        $classFile = $baseDir . $folder . $file;
        if (file_exists($classFile)) {
            $map[$class] = $folder . $file;
        }
    }
}

echo serialize($map);
