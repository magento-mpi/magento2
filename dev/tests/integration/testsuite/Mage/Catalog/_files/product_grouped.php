<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/product_simple_duplicated.php';
require __DIR__ . '/product_virtual.php';

/** @var $product Mage_Catalog_Model_Product */
$product = Mage::getModel('Mage_Catalog_Model_Product');
$product->isObjectNew(true);
$product->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_GROUPED)
    ->setId(9)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Grouped Product')
    ->setSku('grouped-product')
    ->setPrice(100)
    ->setTaxClassId(0)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setGroupedLinkData(array(
        2 => array('qty' => 1, 'position' => 1),
        21 => array('qty' => 1, 'position' => 2),
    ))
    ->save();
