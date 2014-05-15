<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../..');
require __DIR__ . '/../../../../../../../app/autoload.php';
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
    array($rootDir . '/lib/', $rootDir . '/dev/tests/unit/framework/', $rootDir . '/app/code/', $rootDir . '/app')
);
