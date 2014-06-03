<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
$quote->load('test01', 'reserved_order_id');

$payment = $quote->getPayment();
$payment->setMethod(\Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS);
$payment->setAdditionalInformation(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID, 123);

$quote->collectTotals()->save();
