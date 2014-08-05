<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processorResponse.php';

$response = $processor->process404();
$response->sendResponse();
