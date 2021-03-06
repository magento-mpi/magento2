<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Create categories
 */
$categoriesNumber = 3;
$maxNestingLevel = 3;
$anchorStep = 2;

$nestingLevel = 1;
$parentCategoryId = $defaultParentCategoryId = Mage::app()->getStore()->getRootCategoryId();
$nestingPath = "1/{$parentCategoryId}";
$categoryPath = '';
$categoryIndex = 1;

$categoryIds = [];

$category = Mage::getModel('Magento_Catalog_Model_Category');
while ($categoryIndex <= $categoriesNumber) {
    $category->setId(
        null
    )->setName(
        "Sample Category {$categoryIndex}"
    )->setUrlKey(
        null
    )->setParentId(
        $parentCategoryId
    )->setPath(
        $nestingPath
    )->setLevel(
        $nestingLevel
    )->setAvailableSortBy(
        'name'
    )->setDefaultSortBy(
        'name'
    )->setIsActive(
        true
    )->setIsAnchor(
        $categoryIndex++ % $anchorStep == 0
    )->save();

    $categoryPath .= '/' . $category->getName();
    $categoryIds[] = $category->getId();

    if ($nestingLevel++ == $maxNestingLevel) {
        $nestingLevel = 1;
        $parentCategoryId = $defaultParentCategoryId;
        $nestingPath = '1';
        $categoryPath = '';
    } else {
        $parentCategoryId = $category->getId();
    }
    $nestingPath .= "/{$parentCategoryId}";
}

// Extract product set id
$productResource = Mage::getModel('Magento_Catalog_Model_Product');
$entityType = $productResource->getResource()->getEntityType();
$sets = Mage::getResourceModel(
    'Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection'
)->setEntityTypeFilter(
    $entityType->getId()
)->load();

$setId = null;
foreach ($sets as $setInfo) {
    $setId = $setInfo->getId();
    break;
}
if (!$setId) {
    throw new Exception('No attributes sets for product found.');
}

$productsCount = 15;
//number of products
$productCategories = [array_pop($categoryIds)];

/**
 * Skipping fixture generation tool to avoid reindex requirement
 */
while ($productsCount) {
    // Create product
    $product = Mage::getModel('Magento_Catalog_Model_Product');
    $product->setTypeId(
        'simple'
    )->setAttributeSetId(
        $setId
    )->setWebsiteIds(
        [1]
    )->setName(
        "Simple product {$productsCount}"
    )->setShortDescription(
        "Simple product {$productsCount} short description"
    )->setWeight(
        1
    )->setDescription(
        "Simple product {$productsCount} description"
    )->setSku(
        'product_' . $productsCount
    )->setPrice(
        10
    )->setVisibility(
        Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
    )->setStatus(
        Magento_Catalog_Model_Product_Status::STATUS_ENABLED
    )->setTaxClassId(
        2
    )->setCategoryIds(
        $productCategories
    )->save();

    $stockItem = Mage::getModel('Magento_CatalogInventory_Model_Stock_Item');
    $stockItem->setProductId(
        $product->getId()
    )->setTypeId(
        $product->getTypeId()
    )->setIsInStock(
        1
    )->setQty(
        10000
    )->setUseConfigMinQty(
        1
    )->setUseConfigBackorders(
        1
    )->setUseConfigMinSaleQty(
        1
    )->setUseConfigMaxSaleQty(
        1
    )->setUseConfigNotifyStockQty(
        1
    )->setUseConfigManageStock(
        1
    )->setUseConfigQtyIncrements(
        1
    )->setUseConfigEnableQtyInc(
        1
    )->save();
    $productsCount--;
}
