<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
//Set up customer fixture
Mage::app()->loadArea('adminhtml');
require 'customer.php';
/** @var $customer Magento_Customer_Model_Customer */
//Set up customer address fixture
$customer = Mage::registry('customer');
/** @var $customerAddress Magento_Customer_Model_Address */
$customerAddress = Mage::registry('customer_address');
/*//$customerAddress->addShippingRate($rate);
$customerAddress->setShippingMethod('freeshipping_freeshipping');
$customerAddress->addShippingRate($method);   //$rate
$customerAddress->save();*/

//Set up simple product fixture
require 'product_simple.php';
/** @var $product Magento_Catalog_Model_Product */
$product = Mage::registry('product_simple');


//Create quote
$quote = Mage::getModel('Magento_Sales_Model_Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 5);

/** @var $rate Magento_Sales_Model_Quote_Address_Rate */
$rate = Mage::getModel('Magento_Sales_Model_Quote_Address_Rate');
$rate->setCode('freeshipping_freeshipping');
$rate->getPrice(1);

$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
$quote->getShippingAddress()->addShippingRate($rate);

$quote->collectTotals();
$quote->save();
Mage::register('quote', $quote);

//Create order
$quoteService = Magento_TestFramework_ObjectManager::getInstance()->create('Magento_Sales_Model_Service_Quote',
    array('qoute' => $quote));
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
$order = $quoteService->submitOrder();
$order->place();
$order->save();
Mage::register('order', $order);
