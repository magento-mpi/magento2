<?php
/**
 * Toolkit framework bootstrap script
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_toolkit_framework
 * @copyright   {copyright}
 * @license     {license_link}
 */

$toolkitBaseDir = realpath(__DIR__ . '/..');
$magentoBaseDir = realpath($testsBaseDir . '/../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
\Magento\Autoload\IncludePath::addIncludePath("$toolkitBaseDir/framework");

return $magentoBaseDir;
