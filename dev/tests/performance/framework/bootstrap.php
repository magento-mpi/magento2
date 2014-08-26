<?php
/**
 * Performance framework bootstrap script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$testsBaseDir = dirname(__DIR__);
require __DIR__ . '/../../../../app/bootstrap.php';
$appBootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath($testsBaseDir . '/framework');
$bootstrap = new \Magento\TestFramework\Performance\Bootstrap($appBootstrap, $testsBaseDir);
$bootstrap->cleanupReports();
return $bootstrap;
