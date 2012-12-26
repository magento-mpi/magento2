<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'category.php';
/** @var $category Mage_Catalog_Model_Category */
$category = Magento_Test_TestCase_ApiAbstract::getFixture('category');
$assignedProductsFixture = array();
$assignedProducts = array();
for ($i = 0; $i <= 2; $i++) {
    /* @var $product Mage_Catalog_Model_Product */
    $product = require 'API/_fixture/_block/Catalog/Product.php';
//    if ($i == 3) {
//        // disabled product
//        $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
//    } else if ($i == 4) {
//        // product visible only in search
//        $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH);
//    } else if ($i == 5) {
//        // out of stock product
//        $stockItem = $product->getStockItem();
//        $stockItem->setData('is_in_stock', 0);
//    }
    $product->setName("Assigned product #$i");
    $product->save();
    $assignedProductsFixture[] = $product;
    $positionInCategory = $i;
    $assignedProducts[$product->getId()] = $positionInCategory;
}
$category->setPostedProducts($assignedProducts);
$category->save();
Magento_Test_TestCase_ApiAbstract::setFixture('assigned_products', $assignedProductsFixture);
// reload category to make assigned_products available in it
$category = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
Magento_Test_TestCase_ApiAbstract::setFixture(
    'category',
    $category,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);

