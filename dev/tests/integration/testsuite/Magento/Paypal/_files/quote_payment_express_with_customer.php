<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Customer/_files/customer.php';
require __DIR__ . '/../../Customer/_files/customer_two_addresses.php';

\Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea('adminhtml');
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')->getStore()
    ->setConfig('carriers/flatrate/active', 1);
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')->getStore()
    ->setConfig('payment/paypal_express/active', 1);
/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(10)
    ->setStockData(array(
    'use_config_manage_stock' => 1,
    'qty' => 100,
    'is_qty_decimal' => 0,
    'is_in_stock' => 100,
))
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->save();
$product->load(1);

$addressConverter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Address\Converter');

$customerBillingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Address');
$customerBillingAddress->load(1);
$billingAddressDataObject = $addressConverter->createAddressFromModel($customerBillingAddress, false, false);
$billingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Quote\Address');
$billingAddress->importCustomerAddressData($billingAddressDataObject);
$billingAddress->setAddressType('billing');

$customerShippingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Address');
$customerShippingAddress->load(2);
$shippingAddressDataObject = $addressConverter->createAddressFromModel($customerShippingAddress, false, false);
$shippingAddress = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Quote\Address');
$shippingAddress->importCustomerAddressData($shippingAddressDataObject);
$shippingAddress->setAddressType('shipping');

$shippingAddress->setShippingMethod('flatrate_flatrate');
$shippingAddress->setCollectShippingRates(true);

/** @var $quote \Magento\Sales\Model\Quote */
$quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Quote');
$quote->setCustomerIsGuest(false)
    ->setCustomerId($customer->getId())
    ->setCustomer($customer)
    ->setStoreId(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setReservedOrderId('test02')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product, 10);
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getPayment()->setMethod(\Magento\Paypal\Model\Config::METHOD_WPS);
$quote->collectTotals()->save();

/** @var $service \Magento\Sales\Model\Service\Quote */
$service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Service\Quote', array('quote' => $quote));
$service->setOrderData(array('increment_id' => '100000002'));
$service->submitAllWithDataObject();

$order = $service->getOrder();
$order->save();

