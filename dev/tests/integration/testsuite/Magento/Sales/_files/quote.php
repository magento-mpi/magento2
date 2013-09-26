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
Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')->loadArea('frontend');
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_Catalog_Model_Product');
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setStockData(
        array(
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        )
    )
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->save();
$product->load(1);

$addressData = include(__DIR__ . '/address_data.php');
$billingAddress = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Sales_Model_Quote_Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');

$quote = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Sales_Model_Quote');
$quote->setCustomerIsGuest(true)
    ->setStoreId(
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->getId()
    )
    ->setReservedOrderId('test01')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product);
$quote->getPayment()->setMethod('checkmo');
$quote->setIsMultiShipping('1');
$quote->save();
