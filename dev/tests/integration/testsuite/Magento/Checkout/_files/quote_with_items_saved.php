<?php
/**
 * Save quote_with_address fixture
 *
 * The quote is not saved inside the original fixture. It is later saved inside child fixtures, but along with some
 * additional data which may break some tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Checkout/_files/quote_with_address.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setTypeId(
    'simple'
)->setAttributeSetId(
    4
)->setWebsiteIds(
    array(1)
)->setName(
    'Simple Product'
)->setSku(
    'simple_one'
)->setPrice(
    10
)->setMetaTitle(
    'meta title'
)->setMetaKeyword(
    'meta keyword'
)->setMetaDescription(
    'meta description'
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->setStockData(
    array('use_config_manage_stock' => 0)
)->save();
$quoteProduct = $product->load($product->getIdBySku('simple_one'));
$quote->setReservedOrderId('test_order_item_with_items')
    ->addProduct($product->load($product->getIdBySku('simple_one')), 1);
$quote->collectTotals()->save();
