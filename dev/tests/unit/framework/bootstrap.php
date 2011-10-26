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

if (!is_writable(TESTS_TEMP_DIR)) {
    throw new Exception(TESTS_TEMP_DIR . ' must be writable.');
}

$includePaths = array(
    get_include_path(),
    './testsuite',
    '../../../lib',
    '../../../app/code/core'
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));
spl_autoload_register('magentoAutoloadForUnitTests');
register_shutdown_function('magentoCleanTmpForUnitTests');

function magentoAutoloadForUnitTests($class)
{
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
        $fileName = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($fileName)) {
            include $file;
        }
    }
    return false;
}

function magentoCleanTmpForUnitTests()
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(TESTS_TEMP_DIR),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        if (strpos($file->getFilename(), '.') === 0) {
            continue;
        }
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
}
