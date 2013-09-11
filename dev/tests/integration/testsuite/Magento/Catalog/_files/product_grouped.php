<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/product_simple_duplicated.php';
require __DIR__ . '/product_virtual.php';

/** @var $product \Magento\Catalog\Model\Product */
$product = Mage::getModel('\Magento\Catalog\Model\Product');
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_GROUPED)
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
