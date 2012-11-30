<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../../');
require_once $rootDir . '/app/bootstrap.php';

$codeDirs = array(
    $rootDir . '/lib/',
    $rootDir . '/app/code/core/',
    $rootDir . '/dev/tests/integration/framework/',
);
set_include_path(implode(PATH_SEPARATOR, $codeDirs) . PATH_SEPARATOR . get_include_path());

Mage::setRoot();
Mage::initializeObjectManager(null, new Magento_Test_ObjectManager());
