<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (Magento_Test_Helper_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
    return; // MAGETWO-7075
}

Mage::app()->getStore()->setConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, 2);

$stockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item')
    ->addQty(10);

$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setStockItem($stockItem)
    ->setTaxClassId(2)
    ->save();

$addressData = include(__DIR__ . '/address_data.php');
$billingQuoteAddress = Mage::getModel('Mage_Sales_Model_Quote_Address', array('data' => $addressData));
$billingQuoteAddress->setAddressType('billing');

$shippingQuoteAddress = clone $billingQuoteAddress;
$shippingQuoteAddress->setId(null)
    ->setAddressType('shipping');

$billingOrderAddress = Mage::getModel('Mage_Sales_Model_Order_Address', array('data' => $addressData));
$billingOrderAddress->setAddressType('billing');

$shippingOrderAddress = clone $billingOrderAddress;
$shippingOrderAddress->setId(null)
    ->setAddressType('shipping');

$payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
$payment->setMethod('checkmo');

$quote = Mage::getModel('Mage_Sales_Model_Quote');
$quote->setCustomerIsGuest(true)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setReservedOrderId('test01')
    ->setBillingAddress($billingQuoteAddress)
    ->setShippingAddress($shippingQuoteAddress)
    ->addProduct($product)
    ->setPayment($payment);

$quote->save();

$order = Mage::getModel('Mage_Sales_Model_Order');
$order->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setAppliedTaxes(array())
    ->setConvertingFromQuote(true)
    ->setAppliedTaxDetailsIsSaved(false)
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingOrderAddress)
    ->setShippingAddress($shippingOrderAddress)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setQuote($quote)
    ->setPayment($payment);

$quoteConverter = Mage::getModel('Mage_Sales_Model_Convert_Quote');
foreach ($quote->getAllItems() as $item) {
    $orderItem = $quoteConverter->itemToOrderItem($item);
    $order->addItem($orderItem);
}
$order->save();
