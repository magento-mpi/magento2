<?php
/**
 * Paid invoice fixture.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

require 'order.php';
/** @var \Magento\Sales\Model\Order $order */

$orderService = \Magento\TestFramework\ObjectManager::getInstance()->create('Magento\Sales\Model\Service\Order',
    array('order' => $order));
$invoice = $orderService->prepareInvoice();
$invoice->register();
$order->setIsInProcess(true);
$transactionSave = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\DB\Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
