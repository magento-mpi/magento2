<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Mage/Sales/_files/order.php';

/** @var Mage_Sales_Model_Order $order */
$order = Mage::getModel('Mage_Sales_Model_Order');
$order->loadByIncrementId('100000001')
    ->setCouponCode('1234567890')
    ->save();
