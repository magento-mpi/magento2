<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId(
    \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD
)->setId(
    1
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Gift Card'
)->setSku(
    'gift-card'
)->setPrice(
    10
)->setDescription(
    'Gift Card Description'
)->setMetaTitle(
    'Gift Card Meta Title'
)->setMetaKeyword(
    'Gift Card Meta Keyword'
)->setMetaDescription(
    'Gift Card Meta Description'
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->setCategoryIds(
    [2]
)->setStockData(
    ['use_config_manage_stock' => 0]
)->setCanSaveCustomOptions(
    true
)->setHasOptions(
    true
)->setAllowOpenAmount(
    1
)->save();
