<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';
/** @var Magento_Catalog_Model_Product $product */

$addressData = include(__DIR__ . '/address_data.php');
$billingAddress = Mage::getModel('Magento_Sales_Model_Order_Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = Mage::getModel('Magento_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

/** @var Magento_Sales_Model_Order_Item $orderItem */
$orderItem = Mage::getModel('Magento_Sales_Model_Order_Item');
$orderItem->setProductId($product->getId())->setQtyOrdered(2);

/** @var Magento_Sales_Model_Order $order */
$order = Mage::getModel('Magento_Sales_Model_Order');
$order->setIncrementId('100000001')
    ->setState(Magento_Sales_Model_Order::STATE_PROCESSING)
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setCustomerEmail('customer@null.com')
    ->setStoreId(Mage::app()->getStore()->getId())
    ->addItem($orderItem)
    ->setPayment($payment);
$order->save();
