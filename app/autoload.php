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

/* 'composer install' validation */
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Whoops, it looks like Magento application dependencies are not installed.</h3>
    </div>
    <p>Please run 'composer install' under application root directory.</p>
</div>
HTML;
    exit(1);
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
