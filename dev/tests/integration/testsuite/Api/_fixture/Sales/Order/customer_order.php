<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $productFixture Mage_Catalog_Model_Product */
$productFixture = require '_fixture/_block/Catalog/Product.php';

/* @var $quoteFixture Mage_Sales_Model_Quote */
$quoteFixture = require '_fixture/_block/Sales/Quote/Quote.php';

/* @var $rateFixture Mage_Sales_Model_Quote_Address_Rate */
$rateFixture = require '_fixture/_block/Sales/Quote/Rate.php';

/* @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('Mage_Customer_Model_Customer');
$customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

// Create products
$product1 = clone $productFixture;
$product1->save();
$product2 = clone $productFixture;
$product2->save();

// Create quote
$quoteFixture->assignCustomerWithAddressChange($customer);
$quoteFixture->addProduct($product1, 1);
$quoteFixture->addProduct($product2, 2);
$quoteFixture->getShippingAddress()->addShippingRate($rateFixture);
$quoteFixture->collectTotals()
    ->save();

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quoteFixture);
$order = $quoteService->submitOrder()
    ->place()
    ->save();

Mage::register('customer_products', array($product1, $product2));
Mage::register('customer_quote', $quoteFixture);
Mage::register(
    'customer_order',
    Mage::getModel('Mage_Sales_Model_Order')->load($order->getId())
);
