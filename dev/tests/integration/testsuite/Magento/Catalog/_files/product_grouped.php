<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/product_simple_duplicated.php';
require __DIR__ . '/product_virtual.php';

/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->isObjectNew(true);
$product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_GROUPED)
    ->setId(9)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Grouped Product')
    ->setSku('grouped-product')
    ->setPrice(100)
    ->setTaxClassId(0)
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setGroupedLinkData(array(
        2 => array('qty' => 1, 'position' => 1),
        21 => array('qty' => 1, 'position' => 2),
    ))
    ->save();
