<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Sales/_files/order.php';

// refresh report statistics
/** @var \Magento\Sales\Model\Resource\Report\Order $reportResource */
$reportResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Sales\Model\Resource\Report\Order'
);
$reportResource->aggregate();
