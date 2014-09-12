<?php
/**
 * Entry point for static resources (JS, CSS, etc.)
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\StaticResource $app */
$app = $bootstrap->createApplication('Magento\Framework\App\StaticResource');
$bootstrap->run($app);
