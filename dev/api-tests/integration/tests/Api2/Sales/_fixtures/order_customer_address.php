<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');

/* @var $productFixture Mage_Catalog_Model_Product */
$productFixture = require $fixturesDir . '/Catalog/Product.php';

/* @var $quote Mage_Sales_Model_Quote */
$quote = require $fixturesDir . '/Sales/Quote/Quote.php';

/* @var $address Mage_Sales_Model_Quote_Address */
$address = require $fixturesDir . '/Sales/Quote/Address.php';

/* @var $rateFixture Mage_Sales_Model_Quote_Address_Rate */
$rateFixture = require $fixturesDir . '/Sales/Quote/Rate.php';

/* @var $customer Mage_Customer_Model_Customer */
$customer = Mage::getModel('customer/customer');
$customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);


// Create products
$product1 = clone $productFixture;
$product1->save();
$product2 = clone $productFixture;
$product2->save();

// Create quote
$quote->assignCustomer($customer);
$quote->addProduct($product1, 1);
$quote->addProduct($product2, 2);

$shippingAddress = clone $address;
$quote->setShippingAddress(clone $address);

$billingAddress = clone $address;
$quote->setBillingAddress(clone $address);

$quote->getShippingAddress()->addShippingRate($rateFixture);
$quote->collectTotals()
    ->save();

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
$order = $quoteService->submitOrder()
    ->place()
    ->save();

Magento_Test_Webservice::setFixture('customer_product1', $product1);
Magento_Test_Webservice::setFixture('customer_product2', $product2);
Magento_Test_Webservice::setFixture('customer_quote', $quote);
Magento_Test_Webservice::setFixture('customer_order', Mage::getModel('sales/order')->load($order->getId()));
