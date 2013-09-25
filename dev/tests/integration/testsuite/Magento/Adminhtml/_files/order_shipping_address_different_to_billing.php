<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $billingAddress Magento_Sales_Model_Order_Address */
$billingAddress = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
    'Magento_Sales_Model_Order_Address',
    array(
        'data' => array(
            'firstname'  => 'guest',
            'lastname'   => 'guest',
            'email'      => 'customer@example.com',
            'street'     => 'street',
            'city'       => 'Los Angeles',
            'region'     => 'CA',
            'postcode'   => '1',
            'country_id' => 'US',
            'telephone'  => '1',
        )
    )
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setPostcode('2')
    ->setAddressType('shipping');

/** @var $order Magento_Sales_Model_Order */
$order = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Sales_Model_Order');
$order->loadByIncrementId('100000001');
$clonedOrder = clone $order;
$order->setIncrementId('100000002');
$order->save();

/** @var $payment Magento_Sales_Model_Order_Payment */
$payment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

$order = $clonedOrder;
$order->setId(null)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();
