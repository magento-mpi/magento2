<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $category \Magento\Catalog\Model\Category */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $productFirst \Magento\Catalog\Model\Product */
$productFirst = $objectManager->create('Magento\Catalog\Model\Product');
$productFirst->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product First')
    ->setSku('fulltext-1')
    ->setPrice(10)
    ->setMetaTitle('first meta title')
    ->setMetaKeyword('first meta keyword')
    ->setMetaDescription('first meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFirst \Magento\Catalog\Model\Product */
$productSecond = $objectManager->create('Magento\Catalog\Model\Product');
$productSecond->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product Second')
    ->setSku('fulltext-2')
    ->setPrice(20)
    ->setMetaTitle('second meta title')
    ->setMetaKeyword('second meta keyword')
    ->setMetaDescription('second meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

