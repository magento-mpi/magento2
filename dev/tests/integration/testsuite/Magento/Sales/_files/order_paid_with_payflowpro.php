<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$addressData = include __DIR__ . '/address_data.php';

$billingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Sales\Model\Order\Address',
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order\Payment');
$payment->setMethod('payflowpro')->setCcExpMonth('5')->setCcLast4('0005')->setCcType('AE')->setCcExpYear('2016');

$order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
$order->setIncrementId(
    '100000001'
)->setSubtotal(
    100
)->setBaseSubtotal(
    100
)->setCustomerEmail(
    'admin@example.com'
)->setCustomerIsGuest(
    true
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setStoreId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Store\Model\StoreManagerInterface'
    )->getStore()->getId()
)->setPayment(
    $payment
);

$order->save();
