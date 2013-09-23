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

/**
 * Products generation to test base data
 */

$prices = array(5, 10, 15, 20, 50, 100, 150);

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));
/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category Magento_Catalog_Model_Category */
$category = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Category');
$category->setId(3)
    ->setName('Root Category')
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
    ->setName('PLN Category')
    ->setParentId(3)
    ->setPath('1/2/3/4')
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setIsAnchor(true)
    ->setPosition(1)
    ->save();

$lastProductId = 0;
foreach ($prices as $price) {
    $product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
    $productId = $lastProductId + 1;
    $product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE)
        ->setId($productId)
        ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
        ->setStoreId(1)
        ->setWebsiteIds(array(1))
        ->setName('Simple Product ' . $productId)
        ->setSku('simple-' . $productId)
        ->setPrice($price)
        ->setWeight(18)
        ->setCategoryIds(array(4))
        ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
        ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
        ->save();
    ++$lastProductId;
}
