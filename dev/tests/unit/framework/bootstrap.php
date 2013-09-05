<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../../app/code/Magento/Core/functions.php';
require __DIR__ . '/../../../../app/autoload.php';
\Magento\Autoload\IncludePath::addIncludePath(array(
    __DIR__,
    realpath(__DIR__ . '/../testsuite'),
    realpath(__DIR__ . '/../../../../app'),
    realpath(__DIR__ . '/../../../../app/code'),
    realpath(__DIR__ . '/../../../../lib'),
));
define('BP', realpath(__DIR__ . '/../../../../'));
define('TESTS_TEMP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp');
define('DS', DIRECTORY_SEPARATOR);
if (is_dir(TESTS_TEMP_DIR)) {
    \Magento\Io\File::rmdirRecursive(TESTS_TEMP_DIR);
}
mkdir(TESTS_TEMP_DIR);

\Magento\Phrase::setRenderer(new \Magento\Phrase\Renderer\Placeholder());

Mage::setIsSerializable(false);
