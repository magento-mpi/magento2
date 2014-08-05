<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'processorResponse.php';

if (isset($reportData) && is_array($reportData)) {
    $processor->saveReport($reportData);
}
$response = $processor->processReport();
$response->sendResponse();
