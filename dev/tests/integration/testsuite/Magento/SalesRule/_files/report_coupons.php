<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// refresh report statistics
/** @var \Magento\SalesRule\Model\Resource\Report\Rule $reportResource */
$reportResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\SalesRule\Model\Resource\Report\Rule');
$reportResource->beginTransaction(); // prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (\Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
