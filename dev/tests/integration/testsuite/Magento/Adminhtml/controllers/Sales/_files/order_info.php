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

Mage::app()->loadArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML);

/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('Magento\Catalog\Model\Product');
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
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
    ->save();
$product->load(1);

$addressData = include(__DIR__ . DIRECTORY_SEPARATOR . 'address_data.php');

$billingAddress = Mage::getModel('Magento\Sales\Model\Quote\Address', array('data' => $addressData));
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)
    ->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');

/** @var $quote \Magento\Sales\Model\Quote */
$quote = Mage::getModel('Magento\Sales\Model\Quote');
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

/** @var $service \Magento\Sales\Model\Service\Quote */
$service = Mage::getModel('Magento\Sales\Model\Service\Quote', array('quote' => $quote));
$service->setOrderData(array('increment_id' => '100000001'));
$service->submitAll();

$order = $service->getOrder();
$order->save();

$orderItems = $order->getAllItems();

/** @var $item \Magento\Sales\Model\Order\Item */
$item = $orderItems[0];

/** @var $invoice \Magento\Sales\Model\Order\Invoice */
$invoice = Mage::getModel('Magento\Sales\Model\Service\Order', array('order' => $order))
    ->prepareInvoice(array($item->getId() => 10));

$invoice->register();
$invoice->save();

$creditmemo = Mage::getModel('Magento\Sales\Model\Service\Order', array('order' => $order))
    ->prepareInvoiceCreditmemo($invoice, array('qtys' => array($item->getId() => 5)));

foreach ($creditmemo->getAllItems() as $creditmemoItem) {
    //Workaround to return items to stock
    $creditmemoItem->setBackToStock(true);
}

$creditmemo->register();
$creditmemo->save();

$transactionSave = Mage::getModel('Magento\Core\Model\Resource\Transaction')
    ->addObject($creditmemo)
    ->addObject($creditmemo->getOrder());
if ($creditmemo->getInvoice()) {
    $transactionSave->addObject($creditmemo->getInvoice());
}

$transactionSave->save();
