<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/* @var $addressFixture Mage_Sales_Model_Quote_Address */
$addressFixture = require '_fixture/_block/Sales/Quote/Address.php';

$quote = Mage::getModel('Mage_Sales_Model_Quote');
$quote->setStoreId(Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
    ->setIsMultiShipping(false)
    ->setShippingAddress(clone $addressFixture)
    ->setBillingAddress(clone $addressFixture);
// Set payment method to check/money order
$quote->getPayment()->setMethod('checkmo');
return $quote;
