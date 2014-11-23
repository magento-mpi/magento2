<?php
/**
 * Performance framework bootstrap script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/autoload.php';

$testsBaseDir = dirname(__DIR__);
$appBootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$bootstrap = new \Magento\TestFramework\Performance\Bootstrap($appBootstrap, $testsBaseDir);
$bootstrap->cleanupReports();
return $bootstrap;
