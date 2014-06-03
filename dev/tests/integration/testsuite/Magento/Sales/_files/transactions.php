<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$payment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

$order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
$order->setIncrementId(
    '100000001'
)->setSubtotal(
    100
)->setBaseSubtotal(
    100
)->setCustomerIsGuest(
    true
)->setPayment(
    $payment
);

$payment->setTransactionId('trx1');
$payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH);

$order->save();
