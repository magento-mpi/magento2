<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('COUNT_CUSTOMER_ORDERS_LIST', 2);
define('COUNT_NOT_CUSTOMER_ORDERS_LIST', 2);

/* @var $customerModel Mage_Customer_Model_Customer */
$customerModel = Mage::getModel('customer/customer');
$customerModel->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
$customerId = $customerModel->getId();

$ordersList = array();
// Customers orders
for ($i = 0; $i < COUNT_CUSTOMER_ORDERS_LIST; $i++) {
    /* @var $order Mage_Sales_Model_Order */
    $order = Mage::getModel('sales/order')
        ->setCustomerId($customerId)
        ->setBillingAddress(new Mage_Sales_Model_Order_Address());

    /* @var $payment Mage_Sales_Model_Order_Payment */
    $payment = Mage::getModel('sales/order_payment');
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

Magento_Test_Webservice::setFixture('orders_list_customer', $ordersList);
