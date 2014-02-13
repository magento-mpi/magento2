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

// todo: how to configure custom pub/lib
$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $_SERVER);
$entryPoint->run('Magento\App\StaticResource');
