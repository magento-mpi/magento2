<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../../app/code/core/Mage/Core/functions.php';

$includePaths = array(
    "./framework",
    './testsuite',
    '../../../app/',
    '../../../app/code/core',
    '../../../lib',
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $includePaths));
spl_autoload_register(function($class) {
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
        $fileName = $path . DIRECTORY_SEPARATOR . $file;
        if (file_exists($fileName)) {
            include $file;
            if (class_exists($class, false) || interface_exists($class, false)) {
                return true;
            }
        }

    }
    return false;
});

define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
if (is_dir(TESTS_TEMP_DIR)) {
    Varien_Io_File::rmdirRecursive(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);
