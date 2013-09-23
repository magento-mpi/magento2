<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

Mage::app()->loadArea('adminhtml');
Mage::app()->getStore()->setConfig('carriers/flatrate/active', 1);
/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
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
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
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

$billingAddress = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote_Address', array('data' => $billingData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');
$shippingAddress->setCollectShippingRates(true);

/** @var $quote Magento_Sales_Model_Quote */
$quote = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote');
$quote->setCustomerIsGuest(true)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setReservedOrderId('test02')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product, 10);
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->collectTotals()->save();

$payment = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order_Payment');
$payment->setMethod(Magento_Paypal_Model_Config::METHOD_WPS);

$quote->getPayment()->setMethod(Magento_Paypal_Model_Config::METHOD_WPS);

/** @var $service Magento_Sales_Model_Service_Quote */
$service = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Service_Quote', array('quote' => $quote));
$service->setOrderData(array('increment_id' => '100000001'));
$service->submitAll();

$order = $service->getOrder();
$order->setPayment($payment);
$order->save();


