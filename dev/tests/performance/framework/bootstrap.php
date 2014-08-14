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
/** @var \Magento\Framework\App\Bootstrap $appBootstrap */
$appBootstrap = require __DIR__ . "/../../../../app/bootstrap.php";
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath($testsBaseDir . '/framework');
$bootstrap = new \Magento\TestFramework\Performance\Bootstrap($appBootstrap, $testsBaseDir, BP);
$bootstrap->cleanupReports();
return $bootstrap;
