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
require __DIR__ . '/../../autoload.php';
$loader->addIncludePath(array(
    __DIR__,
    __DIR__ . '/../testsuite',
    __DIR__ . '/../../../../app',
    __DIR__ . '/../../../../app/code/core',
    __DIR__ . '/../../../../lib',
));
define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
if (is_dir(TESTS_TEMP_DIR)) {
    Varien_Io_File::rmdirRecursive(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

Mage::setIsSerializable(false);
