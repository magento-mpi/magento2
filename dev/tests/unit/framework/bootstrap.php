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
require __DIR__ . '/../../../../lib/Magento/Autoload.php';
Magento_Autoload::getInstance();

define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
if (is_dir(TESTS_TEMP_DIR)) {
    Varien_Io_File::rmdirRecursive(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

Mage::setIsSerializable(false);
