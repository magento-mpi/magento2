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

/** @var $category Magento_Catalog_Model_Category */
$category = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Category');
$category->setId(3)
    ->setName('Category 1')
    ->setParentId(2) /**/
    ->setPath('1/2/3')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$category = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Category');
$category->setId(4)
    ->setName('Category 2')
    ->setParentId(2) /**/
    ->setPath('1/2/4')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();

$category = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Category');
$category->setId(5)
    ->setName('Old Root')
    ->setParentId(1) /**/
    ->setPath('1/5')
    ->setLevel(1)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(3)
    ->save();


$product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Product');
$product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(10)
    ->setCategoryIds(array(3))
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->save();

