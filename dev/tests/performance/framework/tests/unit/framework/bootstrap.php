<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../../../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
\Magento\Autoload\IncludePath::addIncludePath("$magentoBaseDir/dev/tests/performance/framework");
