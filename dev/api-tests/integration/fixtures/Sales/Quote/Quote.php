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

$fixturesDir = realpath(dirname(__FILE__) . '/../../../fixtures');

/* @var $addressFixture Mage_Sales_Model_Quote_Address */
$addressFixture = require $fixturesDir . '/Sales/Quote/Address.php';

$quote = new Mage_Sales_Model_Quote();
$quote->setStoreId(Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
    ->setIsMultiShipping(false)
    ->setShippingAddress(clone $addressFixture)
    ->setBillingAddress(clone $addressFixture);
// Set payment method to check/money order
$quote->getPayment()->setMethod('checkmo');
return $quote;
