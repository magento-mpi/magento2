<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $product2 \Magento\Catalog\Model\Product */
$product2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product2
    ->setTypeId('simple')
    ->setId(6)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product2')
    ->setSku('simple2')
    ->setPrice(10)
    ->setMetaTitle('meta title2')
    ->setMetaKeyword('meta keyword2')
    ->setMetaDescription('meta description2')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(array('use_config_manage_stock' => 0))
    ->save();

