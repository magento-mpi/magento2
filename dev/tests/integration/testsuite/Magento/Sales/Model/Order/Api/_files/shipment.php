<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

Mage::app()->loadArea('frontend');
include 'order_with_shipping.php';
/** @var \Magento\Sales\Model\Order $order */

$shipment = $order->prepareShipment();
$shipment->register();
$shipment->getOrder()->setIsInProcess(true);
/** @var \Magento\Core\Model\Resource\Transaction $transaction */
$transaction = Mage::getModel('\Magento\Core\Model\Resource\Transaction');
$transaction->addObject($shipment)->addObject($order)->save();

Mage::register('shipment', $shipment);
