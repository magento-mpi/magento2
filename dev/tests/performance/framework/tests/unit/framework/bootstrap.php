<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../../../../../');

require_once "{$magentoBaseDir}/app/bootstrap.php";
$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);
$includePath->addIncludePath("{$magentoBaseDir}/dev/tests/performance/framework");
