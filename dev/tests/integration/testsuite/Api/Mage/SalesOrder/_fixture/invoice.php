<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
if (!Magento_Test_TestCase_ApiAbstract::getFixture('order')) {
    require 'order.php';
}
/** @var $order Mage_Sales_Model_Order */
$order = Magento_Test_TestCase_ApiAbstract::getFixture('order');

$orderService = new Mage_Sales_Model_Service_Order($order);
$invoice = $orderService->prepareInvoice();
$invoice->register();
$invoice->getOrder()->setIsInProcess(true);
$transactionSave = Mage::getModel('Mage_Core_Model_Resource_Transaction');
$transactionSave->addObject($invoice)
    ->addObject($invoice->getOrder())
    ->save();

Magento_Test_TestCase_ApiAbstract::setFixture('invoice', $invoice);
$order2 = Magento_Test_TestCase_ApiAbstract::getFixture('order2');
$orderService2 = new Mage_Sales_Model_Service_Order($order2);
$invoice2 = $orderService2->prepareInvoice();
$invoice2->register();
$invoice2->getOrder()->setIsInProcess(true);
$transactionSave2 = Mage::getModel('Mage_Core_Model_Resource_Transaction');
$transactionSave2->addObject($invoice2)
    ->addObject($invoice2->getOrder())
    ->save();

Magento_Test_TestCase_ApiAbstract::setFixture('invoice2', $invoice2);
