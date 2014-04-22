<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../..');
require __DIR__ . '/../../../../../../../app/autoload.php';
(new \Magento\Autoload\IncludePath())->addIncludePath(
    array($rootDir . '/lib/', $rootDir . '/dev/tests/unit/framework/', $rootDir . '/app/code/', $rootDir . '/app')
);
