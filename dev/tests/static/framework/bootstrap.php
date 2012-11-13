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

$includePath = array(
    __DIR__,
    dirname(__DIR__) . '/testsuite',
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $includePath));
require __DIR__ . '/../../../../lib/Magento/Autoload.php';
Magento_Autoload::getInstance();

Utility_Files::init(new Utility_Files(realpath(__DIR__ . '/../../../..')));
