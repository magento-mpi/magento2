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
/** @var Magento_Sales_Model_Order $order */

$orderService = Magento_TestFramework_ObjectManager::getInstance()->create('Magento_Sales_Model_Service_Order',
    array('order' => $order));
$invoice = $orderService->prepareInvoice();
$invoice->register();
$order->setIsInProcess(true);
$transactionSave = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)->addObject($order)->save();
