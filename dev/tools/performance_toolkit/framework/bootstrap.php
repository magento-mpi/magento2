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
$magentoBaseDir = realpath($toolkitBaseDir . '/../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath("$toolkitBaseDir/framework");

return $magentoBaseDir;
