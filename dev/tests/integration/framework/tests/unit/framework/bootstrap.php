<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../../');
require_once $rootDir . '/app/bootstrap.php';
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath($rootDir . '/dev/tests/integration/framework');
