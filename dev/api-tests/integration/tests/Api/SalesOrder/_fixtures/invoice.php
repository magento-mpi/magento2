<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'order.php';
/** @var $order Mage_Sales_Model_Order */
$order = Magento_Test_Webservice::getFixture('order');

$orderService = new Mage_Sales_Model_Service_Order($order);
$invoice = $orderService->prepareInvoice();
$invoice->register();
$invoice->getOrder()->setIsInProcess(true);
$transactionSave = new Mage_Core_Model_Resource_Transaction();
$transactionSave->addObject($invoice)
    ->addObject($invoice->getOrder())
    ->save();

Magento_Test_Webservice::setFixture('invoice', $invoice);
$order2 = Magento_Test_Webservice::getFixture('order2');
$orderService2 = new Mage_Sales_Model_Service_Order($order2);
$invoice2 = $orderService2->prepareInvoice();
$invoice2->register();
$invoice2->getOrder()->setIsInProcess(true);
$transactionSave2 = new Mage_Core_Model_Resource_Transaction();
$transactionSave2->addObject($invoice2)
    ->addObject($invoice2->getOrder())
    ->save();

Magento_Test_Webservice::setFixture('invoice2', $invoice2);
