<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('COUNT_CUSTOMER_ORDERS_LIST', 2);
define('COUNT_NOT_CUSTOMER_ORDERS_LIST', 2);

/* @var $customerModel Mage_Customer_Model_Customer */
$customerModel = Mage::getModel('Mage_Customer_Model_Customer');
$customerModel->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
$customerId = $customerModel->getId();

$ordersList = array();
// Customers orders
for ($i = 0; $i < COUNT_CUSTOMER_ORDERS_LIST; $i++) {
    /* @var $order Mage_Sales_Model_Order */
    $order = Mage::getModel('Mage_Sales_Model_Order')
        ->setCustomerId($customerId)
        ->setBillingAddress(Mage::getModel('Mage_Sales_Model_Order_Address'));

    /* @var $payment Mage_Sales_Model_Order_Payment */
    $payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
    $payment->setMethod('free')
        ->setOrder($order)
        ->place();

    $order->setPayment($payment); // WARNING: setPayment return Mage_Sales_Model_Order_Payment
    $order->save();

    $ordersList[] = $order;
}

// Not customers orders
for ($i = 0; $i < COUNT_NOT_CUSTOMER_ORDERS_LIST; $i++) {
    /* @var $order Mage_Sales_Model_Order */
    $order = Mage::getModel('Mage_Sales_Model_Order')
        ->setBillingAddress(Mage::getModel('Mage_Sales_Model_Order_Address'));

    /* @var $payment Mage_Sales_Model_Order_Payment */
    $payment = Mage::getModel('Mage_Sales_Model_Order_Payment')
        ->setMethod('free')
        ->setOrder($order)
        ->place();

    $order->setPayment($payment); // WARNING: setPayment return Mage_Sales_Model_Order_Payment
    $order->save();

    $ordersList[] = $order;
}

PHPUnit_Framework_TestCase::setFixture('orders_list_customer', $ordersList);
