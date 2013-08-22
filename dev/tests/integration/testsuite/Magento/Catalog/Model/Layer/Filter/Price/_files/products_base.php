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

$testCases = include(dirname(__FILE__) . '/_algorithm_base_data.php');

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('Magento_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));
/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category Magento_Catalog_Model_Category */
$category = Mage::getModel('Magento_Catalog_Model_Category');
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

$lastProductId = 0;
foreach ($testCases as $index => $testCase) {
    $category = Mage::getModel('Magento_Catalog_Model_Category');
    $position = $index + 1;
    $categoryId = $index + 4;
    $category->setId($categoryId)
        ->setName('Category ' . $position)
        ->setParentId(3)
        ->setPath('1/2/3/' . $categoryId)
        ->setLevel(3)
        ->setAvailableSortBy('name')
        ->setDefaultSortBy('name')
        ->setIsActive(true)
        ->setIsAnchor(true)
        ->setPosition($position)
        ->save();

    foreach ($testCase[0] as $price) {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
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
            ->setCategoryIds(array($categoryId))
            ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->save();
        ++$lastProductId;
    }
}
