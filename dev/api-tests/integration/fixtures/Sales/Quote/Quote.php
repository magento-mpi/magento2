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
