<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var \Magento\Sales\Model\Resource\Report\Invoiced $reportResource */
$reportResource = \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Invoiced');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (\Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
