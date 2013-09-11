<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$addressData = include(__DIR__ . '/../../../Magento/Sales/_files/address_data.php');
/** @var $billingAddress \Magento\Sales\Model\Order\Address */
$billingAddress = Mage::getModel('\Magento\Sales\Model\Order\Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

/** @var $payment \Magento\Sales\Model\Order\Payment */
$payment = Mage::getModel('\Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

/** @var $orderItem \Magento\Sales\Model\Order\Item */
$orderItem = Mage::getModel('\Magento\Sales\Model\Order\Item');
$orderItem->setProductId(1)
    ->setProductType(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setName('product name')
    ->setSku('smp00001')
    ->setBasePrice(100)
    ->setQtyOrdered(1)
    ->setQtyShipped(1)
    ->setIsQtyDecimal(true);

/** @var $order \Magento\Sales\Model\Order */
$order = Mage::getModel('\Magento\Sales\Model\Order');
$order->addItem($orderItem)
    ->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setPayment($payment);
$order->save();
