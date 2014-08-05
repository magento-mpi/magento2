<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processorResponse.php';

$processor = new \Magento\Framework\Error\Processor($response);
$response = $processor->process503();
$response->sendResponse();
