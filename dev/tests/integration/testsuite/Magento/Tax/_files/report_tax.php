<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var \Magento\Tax\Model\Resource\Report\Tax $reportResource */
$reportResource = \Mage::getResourceModel('Magento\Tax\Model\Resource\Report\Tax');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (\Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
