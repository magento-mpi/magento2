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

$processor = new Error_Processor(
    new \Magento\Framework\App\Response\Http(
        new \Magento\Framework\Stdlib\Cookie(),
        new \Magento\Framework\App\Http\Context()
    )
);
$response = $processor->processNoCache();
$response->sendResponse();
