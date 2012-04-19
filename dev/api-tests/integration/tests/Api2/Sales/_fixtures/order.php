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

/* @var $quoteFixture Mage_Sales_Model_Quote */
$quoteFixture = require $fixturesDir . '/Sales/Quote/Quote.php';

/* @var $rateFixture Mage_Sales_Model_Quote_Address_Rate */
$rateFixture = require $fixturesDir . '/Sales/Quote/Rate.php';

// Create products
$product1 = clone $productFixture;
$product1->save();
$product2 = clone $productFixture;
$product2->save();

// Create quote
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

Magento_Test_Webservice::setFixture('products', array($product1, $product2));
Magento_Test_Webservice::setFixture('quote', $quoteFixture);
Magento_Test_Webservice::setFixture('order', Mage::getModel('sales/order')->load($order->getId()));
