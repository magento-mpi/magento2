<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $product Magento_Catalog_Model_Product */
$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Cross Sell')
    ->setSku('simple')
    ->setPrice(10)
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->save();

$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
$product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(2)
    ->setAttributeSetId(4)
    ->setName('Simple Product With Cross Sell')
    ->setSku('simple_with_cross')
    ->setPrice(10)
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setWebsiteIds(array(1))
    ->setStockData(array('qty' => 100, 'is_in_stock' => 1))
    ->setCrossSellLinkData(array(1 => array('position' => 1)))
    ->save();
