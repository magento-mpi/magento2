<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downlodable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

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

$payment = Mage::getModel('\Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

$orderItem = Mage::getModel('\Magento\Sales\Model\Order\Item');
$orderItem->setProductId(1)
    ->setProductType(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    ->setBasePrice(100)
    ->setQtyOrdered(1);

$order = Mage::getModel('\Magento\Sales\Model\Order');
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setCustomerIsGuest(true)
    ->setStoreId(1)
    ->setEmailSent(1)
    ->setBillingAddress($billingAddress)
    ->setPayment($payment);
$order->save();
