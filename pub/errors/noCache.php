<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processor.php';

$processor = new \Magento\Framework\Error\Processor(
    new \Magento\Framework\App\Response\Http(
        new \Magento\Framework\Stdlib\Cookie(),
        new \Magento\Framework\App\Http\Context()
    )
);
$response = $processor->processNoCache();
$response->sendResponse();
