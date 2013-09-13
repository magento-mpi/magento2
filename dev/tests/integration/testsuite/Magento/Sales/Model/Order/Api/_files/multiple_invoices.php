<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

if (!$objectManager->get('Magento_Core_Model_Registry')->registry('order')) {
    require 'order.php';
}
/** @var $order Magento_Sales_Model_Order */
$order = $objectManager->get('Magento_Core_Model_Registry')->registry('order');

$orderService = Magento_TestFramework_ObjectManager::getInstance()->create('Magento_Sales_Model_Service_Order',
    array('order' => $order));
$invoice = $orderService->prepareInvoice();
$invoice->register();
$invoice->getOrder()->setIsInProcess(true);
$transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)
    ->addObject($invoice->getOrder())
    ->save();

$objectManager->get('Magento_Core_Model_Registry')->register('invoice', $invoice);
$order2 = $objectManager->get('Magento_Core_Model_Registry')->registry('order2');
$orderService2 = Magento_TestFramework_ObjectManager::getInstance()->create('Magento_Sales_Model_Service_Order',
    array('order' => $order2));
$invoice2 = $orderService2->prepareInvoice();
$invoice2->register();
$invoice2->getOrder()->setIsInProcess(true);
$transactionSave2 = Mage::getModel('Magento_Core_Model_Resource_Transaction');
$transactionSave2->addObject($invoice2)
    ->addObject($invoice2->getOrder())
    ->save();

$objectManager->get('Magento_Core_Model_Registry')->register('invoice2', $invoice2);
