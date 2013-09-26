<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var Magento_Tax_Model_Resource_Report_Tax $reportResource */
$reportResource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Tax_Model_Resource_Report_Tax');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
