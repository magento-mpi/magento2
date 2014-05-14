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
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'New Product'
)->setSku(
    'simple'
)->setPrice(
    10
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->setWebsiteIds(
    array(1)
)->setStockData(
    array('qty' => 100, 'is_in_stock' => 1)
)->setNewsFromDate(
    date('Y-m-d', strtotime('-2 day'))
)->setNewsNewsToDate(
    date('Y-m-d', strtotime('+2 day'))
)->setDescription(
    'description'
)->setShortDescription(
    'short desc'
)->save();
