<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$addressData = include(__DIR__ . '/address_data.php');

$billingAddress = new Mage_Sales_Model_Order_Address($addressData);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = new Mage_Sales_Model_Order_Payment();
$payment->setMethod('verisign');
$payment->setCcExpMonth('5');
$payment->setCcLast4('0005');
$payment->setCcType('AE');
$payment->setCcExpYear('2016');

$order = new Mage_Sales_Model_Order();
$order->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setPayment($payment)
;

$order->save();
