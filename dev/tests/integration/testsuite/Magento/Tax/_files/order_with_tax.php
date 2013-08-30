<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Sales/_files/order.php';

/** @var Magento_Sales_Model_Order $order */
$order = Mage::getModel('Magento_Sales_Model_Order');
$order->loadByIncrementId('100000001')
    ->setBaseToGlobalRate(2)
    ->save();

/** @var Magento_Tax_Model_Sales_Order_Tax $tax */
$tax = Mage::getModel('Magento_Tax_Model_Sales_Order_Tax');
$tax->setData(array(
    'order_id'          => $order->getId(),
    'code'              => 'tax_code',
    'title'             => 'Tax Title',
    'hidden'            => 0,
    'percent'           => 10,
    'priority'          => 1,
    'position'          => 1,
    'amount'            => 10,
    'base_amount'       => 10,
    'process'           => 1,
    'base_real_amount'  => 10,
));
$tax->save();
