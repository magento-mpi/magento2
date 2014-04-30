<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Errors
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
require_once 'processor.php';

$processor = new \Magento\Framework\Error\Processor(
    new \Magento\Framework\App\Response\Http(
        new \Magento\Framework\Stdlib\Cookie(),
        new \Magento\Framework\App\Http\Context()
    )
);
if (isset($reportData) && is_array($reportData)) {
    $processor->saveReport($reportData);
}
$response = $processor->processReport();
$response->sendResponse();
