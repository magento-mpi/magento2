<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';
/** @var \Magento\Catalog\Model\Product $product */

$addressData = include __DIR__ . '/address_data.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$billingAddress = $objectManager->create('Magento\Sales\Model\Order\Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = $objectManager->create('Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = $objectManager->create('Magento\Sales\Model\Order\Item');
$orderItem->setProductId($product->getId())->setQtyOrdered(2);

/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create('Magento\Sales\Model\Order');
$order->setIncrementId(
    '100000001'
)->setState(
    \Magento\Sales\Model\Order::STATE_PROCESSING, true
)->setSubtotal(
    100
)->setBaseSubtotal(
    100
)->setCustomerIsGuest(
    true
)->setCustomerEmail(
    'customer@null.com'
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setStoreId(
    $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()
)->addItem(
    $orderItem
)->setPayment(
    $payment
);
$order->save();

$payment = $order->getPayment();
$paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Payment\Helper\Data'
)->getInfoBlock(
        $payment
    );
$payment->setBlockMock($paymentInfoBlock);

/** @var \Magento\Sales\Model\Order\Shipment $shipment */
$shipment = $objectManager->create('Magento\Sales\Model\Order\Shipment');
$shipment->setOrder($order);

$packages = array(array('1'), array('2'));

$shipment->addItem($objectManager->create('Magento\Sales\Model\Order\Shipment\Item'));
$shipment->setPackages($packages);
$shipment->setShipmentStatus(\Magento\Sales\Model\Order\Shipment::STATUS_NEW);

$shipment->save();
