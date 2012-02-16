<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
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
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = new Mage_Sales_Model_Order_Payment();
$payment->setMethod(Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED);

$quote = new Mage_Sales_Model_Quote();
$quote->save();

$order = new Mage_Sales_Model_Order();
$order->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment)
    ->setQuoteId($quote->getEntityId());
$order->save();

$session = Mage::getSingleton('Mage_Checkout_Model_Session')
    ->setLastRealOrderId($order->getRealOrderId())
    ->setLastQuoteId($quote->getEntityId());