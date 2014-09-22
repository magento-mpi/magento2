<?php
/**
 * Register basic autoloader that uses include path
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Shortcut constant for the root directory
 */
define('BP', dirname(__DIR__));

$vendorDir = require BP . '/app/etc/vendor_path.php';
$vendorAutoload = BP . "/{$vendorDir}/autoload.php";
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
require_once BP . '/lib/internal/Magento/Framework/Autoload/IncludePath.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
$includePath->addIncludePath([BP . '/app/code', BP . '/lib/internal']);
spl_autoload_register([$includePath, 'load']);
$classMapPath = BP . '/var/classmap.ser';
if (file_exists($classMapPath)) {
    require_once BP . '/lib/internal/Magento/Framework/Autoload/ClassMap.php';
    $classMap = new \Magento\Framework\Autoload\ClassMap(BP);
    $classMap->addMap(unserialize(file_get_contents($classMapPath)));
    spl_autoload_register(array($classMap, 'load'), true, true);
}
