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

require 'quote_with_address.php';

$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getPayment()->setMethod('ccsave');

$quote->collectTotals();
$quote->save();
Mage::register('quote', $quote);

$quoteService = new Mage_Sales_Model_Service_Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('ccsave');
