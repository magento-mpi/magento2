<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processorResponse.php';

$response = $processor->process503();
$response->sendResponse();
