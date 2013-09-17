<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var Magento_SalesRule_Model_Resource_Report_Rule $reportResource */
$reportResource = Mage::getResourceModel('Magento_SalesRule_Model_Resource_Report_Rule');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
