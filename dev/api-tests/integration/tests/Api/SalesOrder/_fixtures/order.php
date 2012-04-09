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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
Magento_Test_Webservice::setFixture('quote', $quote);

//Create order
$quoteService = new Mage_Sales_Model_Service_Quote($quote);
//Set payment method to check/money order
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
$order = $quoteService->submitOrder();
$order->place();
$order->save();
Magento_Test_Webservice::setFixture('order', $order);

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
Magento_Test_Webservice::setFixture('quote2', $quote2);

$quoteService2 = new Mage_Sales_Model_Service_Quote($quote2);
//Set payment method to check/money order
$quoteService2->getQuote()->getPayment()->setMethod('checkmo');
$order2 = $quoteService2->submitOrder();
$order2->place();
$order2->save();
Magento_Test_Webservice::setFixture('order2', $order2);
