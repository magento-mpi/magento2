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
    new \Magento\App\Response\Http(
        new \Magento\Stdlib\Cookie(),
        new \Magento\App\Http\Context()
    )
);
$response = $processor->process503();
$response->sendResponse();
