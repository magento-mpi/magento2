<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');

$includePaths = array(
    get_include_path(),
    "./framework",
    './testsuite',
    '../../../lib',
    '../../../app/code/core',
    '../../../app/'
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));
spl_autoload_register('magentoAutoloadForUnitTests');

function magentoAutoloadForUnitTests($class)
{
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
        $fileName = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($fileName)) {
            include $file;
            if (class_exists($class, false)) {
                return true;
            }
        }

    }
    return false;
}

$tmpDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp';
$instance = new Magento_Test_Environment($tmpDir);
Magento_Test_Environment::setInstance($instance);
$instance->cleanTmpDir()
    ->cleanTmpDirOnShutdown();
