<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
if (!Mage::registry('order')) {
    require 'order.php';
}
/** @var $order Magento_Sales_Model_Order */
$order = Mage::registry('order');

$orderService = new Magento_Sales_Model_Service_Order($order);
$invoice = $orderService->prepareInvoice();
$invoice->register();
$invoice->getOrder()->setIsInProcess(true);
$transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)
    ->addObject($invoice->getOrder())
    ->save();

Mage::register('invoice', $invoice);
$order2 = Mage::registry('order2');
$orderService2 = new Magento_Sales_Model_Service_Order($order2);
$invoice2 = $orderService2->prepareInvoice();
$invoice2->register();
$invoice2->getOrder()->setIsInProcess(true);
$transactionSave2 = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transactionSave2->addObject($invoice2)
    ->addObject($invoice2->getOrder())
    ->save();

Mage::register('invoice2', $invoice2);
