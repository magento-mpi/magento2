<?php
/**
 * Performance framework bootstrap script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$testsBaseDir = realpath(__DIR__ . '/..');
$magentoBaseDir = realpath($testsBaseDir . '/../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
Magento_Autoload::getInstance()->addIncludePath("$testsBaseDir/framework");

$bootstrap = new Magento_Bootstrap($testsBaseDir);
return $bootstrap;
