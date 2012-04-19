<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('COUNT_ORDERS_LIST', 3);

$ordersList = array();
for ($i = 0; $i < COUNT_ORDERS_LIST; $i++) {
    /* @var $order Mage_Sales_Model_Order */
    $order = Mage::getModel('sales/order')
        ->setBillingAddress(new Mage_Sales_Model_Order_Address());

    /* @var $payment Mage_Sales_Model_Order_Payment */
    $payment = Mage::getModel('sales/order_payment')
        ->setMethod('free')
        ->setOrder($order)
        ->place();

    $order->setPayment($payment); // WARNING: setPayment return Mage_Sales_Model_Order_Payment
    $order->save();

    $ordersList[] = $order;
}

Magento_Test_Webservice::setFixture('orders_list', $ordersList);
