<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('BP', realpath(__DIR__ . '/../../../../'));
define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
define('DS', DIRECTORY_SEPARATOR);
require BP . '/app/code/Magento/Core/functions.php';
require BP . '/app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(
    __DIR__,
    realpath(__DIR__ . '/../testsuite'),
    realpath(BP . '/app'),
    realpath(BP . '/app/code'),
    realpath(BP . '/lib'),
));
if (is_dir(TESTS_TEMP_DIR)) {
    Magento_Io_File::rmdirRecursive(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

Magento_Phrase::setRenderer(new Magento_Phrase_Renderer_Placeholder());

Mage::setIsSerializable(false);

function tool_autoloader($className)
{
    if (strpos($className, 'Magento\\Tools\\') === false) {
        return false;
    }
    $filePath = str_replace('\\', DS, str_replace('Magento\\Tools\\', '', $className));
    $filePath = BP . DS . 'dev' . DS . 'tools' . DS . $filePath . '.php';

    if (file_exists($filePath)) {
        include($filePath);
    } else {
        return false;
    }
}
spl_autoload_register('tool_autoloader');
