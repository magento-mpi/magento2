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
/** @var Magento_Sales_Model_Order $order */

$orderService = new Magento_Sales_Model_Service_Order($order);
$invoice = $orderService->prepareInvoice();
/** To allow invoice cancelling it should be created without capturing. */
$invoice->setRequestedCaptureCase(Magento_Sales_Model_Order_Invoice::NOT_CAPTURE)->register();
$order->setIsInProcess(true);
$transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
