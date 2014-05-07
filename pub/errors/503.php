<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once __DIR__ . '/../../app/bootstrap.php';
require_once 'processor.php';

$processor = new Error_Processor(
    new \Magento\Framework\App\Response\Http(
        new \Magento\Framework\Stdlib\Cookie(),
        new \Magento\Framework\App\Http\Context()
    )
);
$response = $processor->process503();
$response->sendResponse();
