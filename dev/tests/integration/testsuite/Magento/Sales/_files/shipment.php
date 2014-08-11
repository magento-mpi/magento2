<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/../../../Magento/Sales/_files/order.php';

$payment = $order->getPayment();
$paymentInfoBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Payment\Helper\Data')
    ->getInfoBlock($payment);
$payment->setBlockMock($paymentInfoBlock);

/** @var \Magento\Sales\Model\Order\Shipment $shipment */
$shipment = $objectManager->create('Magento\Sales\Model\Order\Shipment');
$shipment->setOrder($order);

$packages = [['1'], ['2']];

$shipmentItem = $objectManager->create('Magento\Sales\Model\Order\Shipment\Item');
$shipmentItem->setOrderItem($orderItem);
$shipment->addItem($shipmentItem);
$shipment->setPackages($packages);
$shipment->setShipmentStatus(\Magento\Sales\Model\Order\Shipment::STATUS_NEW);

$shipment->save();