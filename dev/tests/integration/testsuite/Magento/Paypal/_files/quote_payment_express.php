<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ \Mage::app()->loadArea('adminhtml'); \Mage::app()->getStore()->setConfig('carriers/flatrate/active', 1); \Mage::app()->getStore()->setConfig('payment/paypal_express/active', 1);
/** @var $product \Magento\Catalog\Model\Product */
$product = \Mage::getModel('Magento\Catalog\Model\Product');
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
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
    ->save();
$product->load(1);

$addressData = array(
    'region' => 'CA',
    'postcode' => '11111',
    'lastname' => 'lastname',
    'firstname' => 'firstname',
    'street' => 'street',
    'city' => 'Los Angeles',
    'email' => 'admin@example.com',
    'telephone' => '11111111',
    'country_id' => 'US',
);

$billingData = array(
    'address_id' => '',
    'firstname' => 'testname',
    'lastname' => 'lastname',
    'company' => '',
    'email' => 'test@com.com',
    'street' =>
    array(
        0 => 'test1',
        1 => '',
    ),
    'city' => 'Test',
    'region_id' => '1',
    'region' => '',
    'postcode' => '9001',
    'country_id' => 'US',
    'telephone' => '11111111',
    'fax' => '',
    'confirm_password' => '',
    'save_in_address_book' => '1',
    'use_for_shipping' => '1',
);

$billingAddress = \Mage::getModel('Magento\Sales\Model\Quote\Address', array('data' => $billingData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');
$shippingAddress->setCollectShippingRates(true);

/** @var $quote \Magento\Sales\Model\Quote */
$quote = \Mage::getModel('Magento\Sales\Model\Quote');
$quote->setCustomerIsGuest(true)
    ->setStoreId(\Mage::app()->getStore()->getId())
    ->setReservedOrderId('test02')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product, 10);
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getPayment()->setMethod(\Magento\Paypal\Model\Config::METHOD_WPS);
$quote->collectTotals()->save();

/** @var $service \Magento\Sales\Model\Service\Quote */
$service = \Mage::getModel('Magento\Sales\Model\Service\Quote', array('quote' => $quote));
$service->setOrderData(array('increment_id' => '100000002'));
$service->submitAll();

$order = $service->getOrder();
$order->save();


