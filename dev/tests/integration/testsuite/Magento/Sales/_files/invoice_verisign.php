<?php
/**
 * Not paid invoice fixture for online payment method.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

require 'order_paid_with_verisign.php';
/** @var \Magento\Sales\Model\Order $order */

$orderService = new \Magento\Sales\Model\Service\Order($order);
$invoice = $orderService->prepareInvoice();
/** To allow invoice cancelling it should be created without capturing. */
$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::NOT_CAPTURE)->register();
$order->setIsInProcess(true);
$transactionSave = Mage::getModel('Magento\Core\Model\Resource\Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
