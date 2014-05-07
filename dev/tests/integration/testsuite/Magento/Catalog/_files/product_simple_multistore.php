<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require __DIR__ . '/../../Core/_files/store.php';
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var Magento\Store\Model\Store $store */
$store = $objectManager->create('Magento\Store\Model\Store');
$store->load('fixturestore', 'code');

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create('Magento\Catalog\Model\Product');
//$product->isObjectNew(true);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setStoreId(
    1
)->setWebsiteIds(
    array(1)
)->setName(
    'Simple Product One'
)->setSku(
    'simple'
)->setPrice(
    10
)->setWeight(
    18
)->setStockData(
    array('use_config_manage_stock' => 0)
)->setCategoryIds(
    array(9)
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->save();

$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setStoreId(1)
    ->load(1)
    ->setStoreId($store->getId())
    ->setName('StoreTitle')
    ->save();

