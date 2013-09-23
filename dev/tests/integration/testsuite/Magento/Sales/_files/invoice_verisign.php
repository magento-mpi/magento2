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

$orderService = Magento_TestFramework_ObjectManager::getInstance()->create('Magento_Sales_Model_Service_Order',
    array('order' => $order));
$invoice = $orderService->prepareInvoice();
/** To allow invoice cancelling it should be created without capturing. */
$invoice->setRequestedCaptureCase(Magento_Sales_Model_Order_Invoice::NOT_CAPTURE)->register();
$order->setIsInProcess(true);
$transactionSave = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
