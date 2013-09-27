<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Sales/_files/order.php';

/** @var Magento_Sales_Model_Order $order */
$order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Sales_Model_Order');
$order->loadByIncrementId('100000001')
    ->setCouponCode('1234567890')
    ->save();
