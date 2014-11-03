<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
);

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId(
    'virtual'
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'Simple Product'
)->setSku(
    'simple'
)->setPrice(
    10
)->setStoreId(
    1
)->setStockData(
    array('use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 100)
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->save();
$product->load(1);

/** @var $quote \Magento\Sales\Model\Quote */
$quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
$quoteItem = $quote->setCustomerId(
    1
)->setStoreId(
    \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Framework\StoreManagerInterface'
    )->getStore()->getId()
)->setReservedOrderId(
    'test01'
)->addProduct(
    $product,
    10
);
/** @var $quoteItem \Magento\Sales\Model\Quote\Item */
$quoteItem->setQty(1);
$quote->getPayment()->setMethod('checkmo')->save();
$quote->getBillingAddress();
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->collectTotals();
$quote->save();
$quoteItem->setQuote($quote);
$quoteItem->save();
