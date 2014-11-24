<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/../../../../../app/bootstrap.php';

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Tools\Di\App\Compiler');
$bootstrap->run($app);
