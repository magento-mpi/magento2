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

$billingAddress = new Mage_Sales_Model_Order_Address();
$billingAddress->setRegion('CA')
    ->setPostcode('11111')
    ->setFirstname('firstname')
    ->setLastname('lastname')
    ->setStreet('street')
    ->setCity('Los Angeles')
    ->setEmail('admin@example.com')
    ->setTelephone('1111111111')
    ->setCountryId('US')
    ->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = new Mage_Sales_Model_Order_Payment();
$payment->setMethod('checkmo');

$order = new Mage_Sales_Model_Order();
$order->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();

$rma = new Enterprise_Rma_Model_Rma();
$rma->setOrderId($order->getId());
$rma->setIncrementId(1);
$rma->save();

$trackingNumber = new Enterprise_Rma_Model_Shipping();
$trackingNumber
    ->setRmaEntityId($rma->getId())
    ->setCarrierTitle('CarrierTitle')
    ->setTrackNumber('TrackNumber')
;
$trackingNumber->save();
