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

$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $_SERVER);
$entryPoint->run('Magento\Framework\App\StaticResource');
