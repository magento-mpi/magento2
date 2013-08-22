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

Mage::app()->loadArea(Magento_Core_Model_App_Area::AREA_ADMINHTML);

/** @var $product Magento_Catalog_Model_Product */
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setTypeId('virtual')
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

$addressData = include(__DIR__ . DIRECTORY_SEPARATOR . 'address_data.php');

$billingAddress = Mage::getModel('Magento_Sales_Model_Quote_Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');

/** @var $quote Magento_Sales_Model_Quote */
$quote = Mage::getModel('Magento_Sales_Model_Quote');
$quote->setCustomerIsGuest(true)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setReservedOrderId('test01')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product, 10);
$quote->getPayment()->setMethod('checkmo');
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->save();

$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getShippingAddress()->collectShippingRates();
$quote->collectTotals();
$quote->save();

/** @var $service Magento_Sales_Model_Service_Quote */
$service = Mage::getModel('Magento_Sales_Model_Service_Quote', array('quote' => $quote));
$service->setOrderData(array('increment_id' => '100000001'));
$service->submitAll();

$order = $service->getOrder();
$order->save();

$orderItems = $order->getAllItems();

/** @var $item Magento_Sales_Model_Order_Item */
$item = $orderItems[0];

/** @var $invoice Magento_Sales_Model_Order_Invoice */
$invoice = Mage::getModel('Magento_Sales_Model_Service_Order', array('order' => $order))
    ->prepareInvoice(array($item->getId() => 10));

$invoice->register();
$invoice->save();

$creditmemo = Mage::getModel('Magento_Sales_Model_Service_Order', array('order' => $order))
    ->prepareInvoiceCreditmemo($invoice, array('qtys' => array($item->getId() => 5)));

foreach ($creditmemo->getAllItems() as $creditmemoItem) {
    //Workaround to return items to stock
    $creditmemoItem->setBackToStock(true);
}

$creditmemo->register();
$creditmemo->save();

$transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
    ->addObject($creditmemo)
    ->addObject($creditmemo->getOrder());
if ($creditmemo->getInvoice()) {
    $transactionSave->addObject($creditmemo->getInvoice());
}

$transactionSave->save();
