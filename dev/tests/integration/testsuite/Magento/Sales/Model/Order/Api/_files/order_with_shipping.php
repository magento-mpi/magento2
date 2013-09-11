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
/** @var $customer \Magento\Customer\Model\Customer */
//Set up customer address fixture
$customer = Mage::registry('customer');
/** @var $customerAddress \Magento\Customer\Model\Address */
$customerAddress = Mage::registry('customer_address');
/*//$customerAddress->addShippingRate($rate);
$customerAddress->setShippingMethod('freeshipping_freeshipping');
$customerAddress->addShippingRate($method);   //$rate
$customerAddress->save();*/

//Set up simple product fixture
require 'product_simple.php';
/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::registry('product_simple');


//Create quote
$quote = Mage::getModel('Magento\Sales\Model\Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customer)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->addProduct($product->load($product->getId()), 5);

/** @var $rate \Magento\Sales\Model\Quote\Address\Rate */
$rate = Mage::getModel('Magento\Sales\Model\Quote\Address\Rate');
$rate->setCode('freeshipping_freeshipping');
$rate->getPrice(1);

$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
$quote->getShippingAddress()->addShippingRate($rate);

$quote->collectTotals();
$quote->save();
Mage::register('quote', $quote);

//Create order
$quoteService = new \Magento\Sales\Model\Service\Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
$order = $quoteService->submitOrder();
$order->place();
$order->save();
Mage::register('order', $order);
