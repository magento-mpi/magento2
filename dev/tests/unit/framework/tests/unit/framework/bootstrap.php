<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../..');
require __DIR__ . '/../../../../../autoload.php';
$loader->addIncludePath(array(
    $rootDir . '/lib/',
    $rootDir . '/dev/tests/unit/framework/',
    $rootDir . '/app/code/core/',
));
