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

$loader = require __DIR__ . '/../../autoload.php';
$loader->addIncludePath(array(__DIR__, dirname(__DIR__) . '/testsuite'));
Utility_Files::init(new Utility_Files(realpath(__DIR__ . '/../../../..')));
