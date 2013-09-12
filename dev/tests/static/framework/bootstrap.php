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

define('BASE_DIR', realpath(__DIR__ . '/../../../../'));
require BASE_DIR . '/app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(
    __DIR__,
    dirname(__DIR__) . '/testsuite',
    BASE_DIR . '/lib',
));
Magento_TestFramework_Utility_Files::init(new Magento_TestFramework_Utility_Files(BASE_DIR));
