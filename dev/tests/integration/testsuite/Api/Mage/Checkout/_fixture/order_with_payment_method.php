<?php
/**
 * Order with payment method fixture.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $customer Mage_Customer_Model_Customer */
$customer = require 'Api/_fixture/_block/Customer/Customer.php';

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require 'Api/_fixture/_block/Customer/Address.php';
$customer->save();

//Set customer default shipping and billing address
$customer->addAddress($customerAddress);
$customer->setDefaultShipping($customerAddress->getId());
$customer->setDefaultBilling($customerAddress->getId());
$customer->save();

//Set up simple product fixture
$product = require 'product_simple.php';

/** @var Mage_Sales_Model_Quote_Address $quoteShippingAddress */
$quoteShippingAddress = Mage::getModel('Mage_Sales_Model_Quote_Address');
$quoteShippingAddress->importCustomerAddress($customerAddressFixture);

//Create quote
$quote = Mage::getModel('Mage_Sales_Model_Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setShippingAddress($quoteShippingAddress)
    ->setBillingAddress($quoteShippingAddress)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getPayment()->setMethod('ccsave');

$quote->collectTotals();
$quote->save();
Mage::register('quote', $quote);

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('ccsave');
