<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Errors
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processor.php';

$processor = new Error_Processor(new \Magento\App\Response\Http());
$response = $processor->processNoCache();
$response->sendResponse();