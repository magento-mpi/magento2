<?php
/**
 * Not paid invoice fixture for online payment method.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

require 'order_paid_with_payflowpro.php';
/** @var \Magento\Sales\Model\Order $order */

$orderService = \Magento\TestFramework\ObjectManager::getInstance()->create(
    'Magento\Sales\Model\Service\Order',
    array('order' => $order)
);
$invoice = $orderService->prepareInvoice();
/** To allow invoice cancelling it should be created without capturing. */
$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::NOT_CAPTURE)->register();
$order->setIsInProcess(true);
$transactionSave = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Framework\DB\Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
