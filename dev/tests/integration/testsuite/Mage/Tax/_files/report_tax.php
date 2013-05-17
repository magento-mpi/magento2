<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var Mage_Tax_Model_Resource_Report_Tax $reportResource */
$reportResource = Mage::getResourceModel('Mage_Tax_Model_Resource_Report_Tax');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
