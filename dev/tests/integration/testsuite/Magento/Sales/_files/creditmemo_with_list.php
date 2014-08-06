<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/order.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create('Magento\Sales\Model\Order');
$order->loadByIncrementId('100000001');

/** @var Magento\Sales\Model\Service\Order  $service */
$service = $objectManager->get('Magento\Sales\Model\Service\Order');
$creditmemo = $service->prepareCreditmemo($order->getData());
$creditmemo->setOrder($order);
$creditmemo->setState('pending');
$creditmemo->save();
