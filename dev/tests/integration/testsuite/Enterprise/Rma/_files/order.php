<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$addressData = include(__DIR__ . '/../../../Mage/Sales/_files/address_data.php');
/** @var $billingAddress Mage_Sales_Model_Order_Address */
$billingAddress = Mage::getModel('Mage_Sales_Model_Order_Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

/** @var $payment Mage_Sales_Model_Order_Payment */
$payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

/** @var $orderItem Mage_Sales_Model_Order_Item */
$orderItem = Mage::getModel('Mage_Sales_Model_Order_Item');
$orderItem->setProductId(1)
    ->setProductType(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setName('product name')
    ->setSku('smp00001')
    ->setBasePrice(100)
    ->setQtyOrdered(1)
    ->setQtyShipped(1)
    ->setIsQtyDecimal(true);

/** @var $order Mage_Sales_Model_Order */
$order = Mage::getModel('Mage_Sales_Model_Order');
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setPayment($payment);
$order->save();
