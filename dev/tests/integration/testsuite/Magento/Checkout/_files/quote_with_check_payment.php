<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'quote_with_address.php';
/** @var Magento_Sales_Model_Quote $quote */

/** @var $rate Magento_Sales_Model_Quote_Address_Rate */
$rate = Mage::getModel('Magento_Sales_Model_Quote_Address_Rate');
$rate->setCode('freeshipping_freeshipping');
$rate->getPrice(1);

$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
$quote->getShippingAddress()->addShippingRate($rate);
$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();

$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$quoteService = $objectManager->create('Magento_Sales_Model_Service_Quote', array('quote' => $quote));
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
