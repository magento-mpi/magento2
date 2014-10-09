<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../../../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
(new \Magento\Framework\Autoload\IncludePath())->addIncludePath(
    "$magentoBaseDir/dev/tools/performance-toolkit/framework"
);
