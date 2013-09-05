<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     tools
 * @copyright   {copyright}
 * @license     {license_link}
 */

$classes = get_declared_classes();
foreach ($classes as $index => $class) {
    if (strpos($class, '_') === false) {
        unset($classes[$index]);
    }
}
sort($classes);
$file = __DIR__ . '/log/magento' . trim(str_replace('/', '_', $_SERVER['REQUEST_URI']), '_') . '.ser';
file_put_contents($file, serialize($classes));
