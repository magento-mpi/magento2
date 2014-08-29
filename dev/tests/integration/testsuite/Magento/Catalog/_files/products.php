<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId(
    'simple'
)->setId(
    1
)->setAttributeSetId(
    4
)->setWebsiteIds(
    array(1)
)->setName(
    'Simple Product'
)->setSku(
    'simple'
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

$customDesignProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Product',
    array('data' => $product->getData())
);
$customDesignProduct->setId(2)->setCustomDesign('Magento/blank')->save();

/** @var $product2 \Magento\Catalog\Model\Product */
$product2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product2->setTypeId(
    'simple'
)->setId(
    2
)->setAttributeSetId(
    4
)->setWebsiteIds(
    array(1)
)->setName(
    'Simple Product2'
)->setSku(
    'simple2'
)->setPrice(
    10
)->setMetaTitle(
    'meta title2'
)->setMetaKeyword(
    'meta keyword2'
)->setMetaDescription(
    'meta description2'
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->setStockData(
    array('use_config_manage_stock' => 0)
)->save();

$customDesignProduct2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Product',
    array('data' => $product2->getData())
);
$customDesignProduct2->setId(3)->setCustomDesign('Magento/blank')->save();
