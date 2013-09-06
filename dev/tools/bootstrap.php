<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
require_once __DIR__ . '/../../app/autoload.php';
define('BP', __DIR__ . '/../..');
define('DS', DIRECTORY_SEPARATOR);
Magento_Autoload_IncludePath::addIncludePath(array(
    BP . DS . 'app' . DS . 'code',
    BP . DS . 'lib',
    BP . DS . 'dev/lib',
));

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', DIRECTORY_SEPARATOR, str_replace('Magento\\Tools\\', '', $className));
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . $filePath . '.php';
    if (file_exists($filePath)) {
        include($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');
