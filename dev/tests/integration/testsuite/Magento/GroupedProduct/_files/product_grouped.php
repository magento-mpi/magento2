<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require realpath(__DIR__ . '/../../') . '/Catalog/_files/product_simple_duplicated.php';
require realpath(__DIR__ . '/../../') . '/Catalog/_files/product_virtual.php';

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->isObjectNew(true);
$product->setTypeId(\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE)
    ->setId(9)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Grouped Product')
    ->setSku('grouped-product')
    ->setPrice(100)
    ->setTaxClassId(0)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
    ->setGroupedLinkData(array(
        2 => array('qty' => 1, 'position' => 1),
        21 => array('qty' => 1, 'position' => 2),
    ))
    ->save();
