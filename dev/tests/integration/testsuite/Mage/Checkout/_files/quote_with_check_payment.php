<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Customer/_files/customer.php';
/** @var Mage_Customer_Model_Customer $customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->load(1);

require __DIR__ . '/../../Customer/_files/customer_address.php';
/** @var Mage_Customer_Model_Address $customerAddress */
$customerAddress = Mage::getModel('Mage_Customer_Model_Address');
$customerAddress->load(1);

require __DIR__ . '/../../Catalog/_files/products.php';
/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->load(1);

/** @var Mage_Sales_Model_Quote_Address $quoteShippingAddress */
$quoteShippingAddress = Mage::getModel('Mage_Sales_Model_Quote_Address');
$quoteShippingAddress->importCustomerAddress($customerAddress);

/** @var Mage_Sales_Model_Quote $quote */
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

/** @var $rate Mage_Sales_Model_Quote_Address_Rate */
$rate = Mage::getModel('Mage_Sales_Model_Quote_Address_Rate');
$rate->setCode('freeshipping_freeshipping');
$rate->getPrice(1);

$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
$quote->getShippingAddress()->addShippingRate($rate);
$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();
Mage::register('quote', $quote);

$quoteService = new Mage_Sales_Model_Service_Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
