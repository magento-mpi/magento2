<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var Magento_Sales_Model_Resource_Report_Bestsellers $reportResource */
$reportResource = Mage::getResourceModel('Magento_Sales_Model_Resource_Report_Bestsellers');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
