<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$addressData = array(
    'region'     => 'CA',
    'postcode'   => '11111',
    'street'     => 'street',
    'city'       => 'Los Angeles',
    'telephone'  => '11111111',
    'country_id' => 'US',
);
$billingAddress = Mage::getModel('\Magento\Sales\Model\Order\Address', array('data' => $addressData));
$shippingAddress = clone $billingAddress;

$item = Mage::getModel('\Magento\Sales\Model\Order\Item');
$item->setOriginalPrice(100)
    ->setPrice(100)
    ->setQtyOrdered(1)
    ->setRowTotal(100)
    ->setSubtotal(100);

$payment = Mage::getModel('\Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

$order = Mage::getModel('\Magento\Sales\Model\Order');
$order->setBaseSubtotal(100)
    ->setSubtotal(100)
    ->setBaseGrandTotal(100)
    ->setGrandTotal(100)
    ->setTotalPaid(100)
    ->setCustomerIsGuest(true)
    ->setState(\Magento\Sales\Model\Order::STATE_NEW, true)
    ->setStoreId(Mage::app()->getStore()->getId());

for ($i = 1; $i <= 100000; $i++) {
    $billingAddress
        ->setId(null)
        ->setFirstname("Name $i")
        ->setLastname("Lastname $i")
        ->setEmail("customer$i@example.com");
    $shippingAddress
        ->setId(null)
        ->setFirstname("Name $i")
        ->setLastname("Lastname $i")
        ->setEmail("customer$i@example.com");

    $item
        ->setId(null)
        ->setSku("item$i");

    $payment->setId(null);

    $order->setId(null);

    $order->getItemsCollection()->removeAllItems();
    $order->getAddressesCollection()->removeAllItems();
    $order->getPaymentsCollection()->removeAllItems();

    $order
        ->setIncrementId((string)(10000000 + $i))
        ->setCreatedAt(date('Y-m-d H:i:s'))
        ->setCustomerEmail("customer$i@example.com")
        ->setBillingAddress($billingAddress)
        ->setShippingAddress($shippingAddress)
        ->addItem($item)
        ->setPayment($payment);

    $order->save();
}
