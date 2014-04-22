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

$processor = new Error_Processor(
    new \Magento\Framework\App\Response\Http(
        new \Magento\Stdlib\Cookie(),
        new \Magento\Framework\App\Http\Context()
    )
);
$response = $processor->process404();
$response->sendResponse();
