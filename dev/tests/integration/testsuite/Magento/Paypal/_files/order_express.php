<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$addressData = include(__DIR__ . '/address_data.php');
$billingAddress = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Payment');
$payment->setMethod(Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS);

$order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
$order->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setBaseGrandTotal(100)
    ->setBaseCurrencyCode('USD')
    ->setCustomerIsGuest(true)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();
