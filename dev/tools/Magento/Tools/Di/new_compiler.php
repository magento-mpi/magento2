<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/../../../../../app/bootstrap.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
$includePath->addIncludePath([BP . '/dev/tools']);
spl_autoload_register([$includePath, 'load']);

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Tools\Di\App\Compiler');
$bootstrap->run($app);
