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

require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';
/** @var \Magento\Catalog\Model\Product $product */

$addressData = include(__DIR__ . '/address_data.php');
$billingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order\Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$payment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order\Payment');
$payment->setMethod('checkmo');

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order\Item');
$orderItem->setProductId($product->getId())->setQtyOrdered(2);

/** @var \Magento\Sales\Model\Order $order */
$order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order');
$order->setIncrementId('100000001')
    ->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setCustomerEmail('customer@null.com')
    ->setStoreId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    )
    ->addItem($orderItem)
    ->setPayment($payment);
$order->save();
