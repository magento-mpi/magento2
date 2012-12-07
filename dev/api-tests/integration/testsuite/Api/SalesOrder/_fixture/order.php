<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

Mage::init('base', 'website');
//Set up customer fixture
require 'customer.php';
/** @var $customer Mage_Customer_Model_Customer */
$customer = Magento_Test_Webservice::getFixture('customer');
//Set up virtual product fixture
require 'product_virtual.php';
/** @var $product Mage_Catalog_Model_Product */
$product = Magento_Test_Webservice::getFixture('product_virtual');

//Create quote
$quote = new Mage_Sales_Model_Quote();
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote->collectTotals();
$quote->save();
Magento_Test_Webservice::setFixture('quote', $quote, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
$order = $quoteService->submitOrder();
$order->place();
$order->save();
Magento_Test_Webservice::setFixture('order', $order, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

//Create order
$quote2 = new Mage_Sales_Model_Quote();
$quote2->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote2->collectTotals();
$quote2->save();
Magento_Test_Webservice::setFixture('quote2', $quote2, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

$quoteService2 = new Mage_Sales_Model_Service_Quote($quote2);
//Set payment method to check/money order
$quoteService2->getQuote()->getPayment()->setMethod('checkmo');
$order2 = $quoteService2->submitOrder();
$order2->place();
$order2->save();
Magento_Test_Webservice::setFixture('order2', $order2, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
