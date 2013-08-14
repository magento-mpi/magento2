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

$processor = new Error_Processor();

if (isset($reportData) && is_array($reportData)) {
    $processor->saveReport($reportData);
}

$processor->processReport();
