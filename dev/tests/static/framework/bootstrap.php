<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$baseDir = realpath(__DIR__ . '/../../../../');
require $baseDir . '/app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(
    __DIR__,
    dirname(__DIR__) . '/testsuite',
    $baseDir . '/lib',
    $baseDir . '/dev/lib',
));
Utility_Files::init(new Utility_Files($baseDir));
