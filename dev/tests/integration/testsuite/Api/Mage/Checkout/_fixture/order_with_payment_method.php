<?php
/**
 * Order with payment method fixture.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $customerFixture Mage_Customer_Model_Customer */
$customerFixture = require '_fixture/_block/Customer/Customer.php';

/* @var $customerAddressFixture Mage_Customer_Model_Address */
$customerAddressFixture = require '_fixture/_block/Customer/Address.php';
$customerFixture->save();

//Set customer default shipping and billing address
$customer->addAddress($customerAddress);
$customer->setDefaultShipping($customerAddress->getId());
$customer->setDefaultBilling($customerAddress->getId());
$customer->save();

Magento_Test_TestCase_ApiAbstract::setFixture(
    'customer',
    Mage::getModel('Mage_Customer_Model_Customer')->load($customerFixture->getId()),
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
); // for load addresses collection

//Set up simple product fixture
require_once 'product_simple.php';
/** @var $product Mage_Catalog_Model_Product */
$product = Magento_Test_TestCase_ApiAbstract::getFixture('product_simple');


//Create quote
$quote = Mage::getModel('Mage_Sales_Model_Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customerFixture)
    ->setShippingAddress($customerAddress)
    ->setBillingAddress($customerAddress)
    ->setCheckoutMethod($customerFixture->getMode())
    ->setPasswordHash($customerFixture->encryptPassword($customerFixture->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getPayment()->setMethod('ccsave');

$quote->collectTotals();
$quote->save();
Magento_Test_TestCase_ApiAbstract::setFixture(
    'quote',
    $quote,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('ccsave');

Magento_Test_TestCase_ApiAbstract::setFixture(
    'order',
    $order,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);
