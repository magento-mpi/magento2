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
if (isset($reportData) && is_array($reportData)) {
    $processor->saveReport($reportData);
}
$response = $processor->processReport();
$response->sendResponse();
