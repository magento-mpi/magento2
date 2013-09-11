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

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = Mage::getResourceModel('\Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'catalog_setup'));
/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category \Magento\Catalog\Model\Category */
$category = Mage::getModel('\Magento\Catalog\Model\Category');
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
    $category = Mage::getModel('\Magento\Catalog\Model\Category');
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
        $product = Mage::getModel('\Magento\Catalog\Model\Product');
        $productId = $lastProductId + 1;
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->setId($productId)
            ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
            ->setStoreId(1)
            ->setWebsiteIds(array(1))
            ->setName('Simple Product ' . $productId)
            ->setSku('simple-' . $productId)
            ->setPrice($price)
            ->setWeight(18)
            ->setCategoryIds(array($categoryId))
            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
            ->save();
        ++$lastProductId;
    }
}
