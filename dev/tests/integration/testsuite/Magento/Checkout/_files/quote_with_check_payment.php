<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'quote_with_address.php';
/** @var \Magento\Sales\Model\Quote $quote */

/** @var $rate \Magento\Sales\Model\Quote\Address\Rate */
$rate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote\Address\Rate');
$rate->setCode('freeshipping_freeshipping');
$rate->getPrice(1);

$quote->getShippingAddress()->setShippingMethod('freeshipping_freeshipping');
$quote->getShippingAddress()->addShippingRate($rate);
$quote->getPayment()->setMethod('checkmo')->save();

$quote->collectTotals();
$quote->save();

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quoteService = $objectManager->create('Magento\Sales\Model\Service\Quote', array('quote' => $quote));
$quoteService->getQuote()->getPayment()->setMethod('checkmo')->save();
