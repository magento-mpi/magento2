<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
require_once 'processor.php';

$locatorFactory = new \Magento\Framework\App\ObjectManagerFactory();
$locator = $locatorFactory->create(BP, $_SERVER);
$response = $locator->create('\Magento\Framework\App\Response\Http');
$processor = new \Magento\Framework\Error\Processor($response);
