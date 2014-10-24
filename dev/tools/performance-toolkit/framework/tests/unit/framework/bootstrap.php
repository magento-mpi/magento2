<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../../../../../');

require_once "$magentoBaseDir/app/bootstrap.php";

\Magento\Framework\Code\Generator\FileResolver::addIncludePath(
    "$magentoBaseDir/dev/tools/performance-toolkit/framework"
);
