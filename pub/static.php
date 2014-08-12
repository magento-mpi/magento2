<?php
/**
 * Entry point for static resources (JS, CSS, etc.)
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require __DIR__ . '/../app/bootstrap.php';
/** @var \Magento\Framework\App\StaticResource $app */
$app = $bootstrap->createApplication('Magento\Framework\App\StaticResource');
$bootstrap->run($app);
