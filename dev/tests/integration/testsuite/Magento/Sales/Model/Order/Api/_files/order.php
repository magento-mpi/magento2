<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
Mage::app()->loadArea('frontend');
//Set up customer fixture
require 'customer.php';
/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();

/** @var $customer Magento_Customer_Model_Customer */
$customer = $objectManager->get('Magento_Core_Model_Registry')->registry('customer');
//Set up virtual product fixture
require 'product_virtual.php';
/** @var $product Magento_Catalog_Model_Product */
$product = $objectManager->get('Magento_Core_Model_Registry')->registry('product_virtual');

//Create quote
$quote = Mage::getModel('Magento_Sales_Model_Quote');
$quote->setStoreId(1)
    ->setCustomerEmail($customer->getEmail())
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote->collectTotals();
$quote->save();
$objectManager->get('Magento_Core_Model_Registry')->register('quote', $quote);

//Create order
$quoteService = new Magento_Sales_Model_Service_Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
$order = $quoteService->submitOrder();
$order->place();
$order->save();
$objectManager->get('Magento_Core_Model_Registry')->register('order', $order);

//Create order
$quote2 = Mage::getModel('Magento_Sales_Model_Quote');
$quote2->setStoreId(1)
    ->setCustomerEmail($customer->getEmail())
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 2);

$quote2->collectTotals();
$quote2->save();

$objectManager->get('Magento_Core_Model_Registry')->register('quote2', $quote2);

$quoteService2 = new Magento_Sales_Model_Service_Quote($quote2);
//Set payment method to check/money order
$quoteService2->getQuote()->getPayment()->setMethod('checkmo');
$order2 = $quoteService2->submitOrder();
$order2->place();
$order2->save();
$objectManager->get('Magento_Core_Model_Registry')->register('order2', $order2);
