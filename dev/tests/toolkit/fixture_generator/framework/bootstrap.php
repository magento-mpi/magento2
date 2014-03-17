<?php
/**
 * Toolkit framework bootstrap script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     toolkit_framework
 * @copyright   {copyright}
 * @license     {license_link}
 */

$testsBaseDir = realpath(__DIR__ . '/..');
$magentoBaseDir = realpath($testsBaseDir . '/../../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
\Magento\Autoload\IncludePath::addIncludePath("$testsBaseDir/framework");

return $magentoBaseDir;
