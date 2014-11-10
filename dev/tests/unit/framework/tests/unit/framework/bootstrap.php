<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../..');
require __DIR__ . '/../../../../../../../app/autoload.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);
$includePath->addIncludePath(
    array(
        $rootDir . '/lib/internal',
        $rootDir . '/dev/tests/unit/framework',
        $rootDir . '/app/code',
        $rootDir . '/app',
    )
);
