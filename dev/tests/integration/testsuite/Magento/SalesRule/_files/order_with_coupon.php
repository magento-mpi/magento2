<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Sales/_files/order.php';

/** @var \Magento\Sales\Model\Order $order */
$order = \Mage::getModel('Magento\Sales\Model\Order');
$order->loadByIncrementId('100000001')
    ->setCouponCode('1234567890')
    ->save();
