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

require __DIR__ . '/../../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(array(__DIR__, dirname(__DIR__) . '/testsuite'));
Utility_Files::init(new Utility_Files(realpath(__DIR__ . '/../../../..')));
