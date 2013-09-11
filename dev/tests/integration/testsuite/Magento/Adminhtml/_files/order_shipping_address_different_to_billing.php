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

/** @var $billingAddress \Magento\Sales\Model\Order\Address */
$billingAddress = Mage::getModel('\Magento\Sales\Model\Order\Address',
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

/** @var $order \Magento\Sales\Model\Order */
$order = Mage::getModel('\Magento\Sales\Model\Order');
$order->loadByIncrementId('100000001');
$clonedOrder = clone $order;
$order->setIncrementId('100000002');
$order->save();

/** @var $payment \Magento\Sales\Model\Order\Payment */
$payment = Mage::getModel('\Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

$order = $clonedOrder;
$order->setId(null)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();
