<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processorFactory.php';

$processorFactory = new \Magento\Framework\Error\ProcessorFactory();
$processor = $processorFactory->createProcessor();
$response = $processor->processNoCache();
$response->sendResponse();
