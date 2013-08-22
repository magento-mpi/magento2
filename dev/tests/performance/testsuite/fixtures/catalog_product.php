<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Extract product set id
$productResource = Mage::getModel('Magento_Catalog_Model_Product');
$entityType = $productResource->getResource()->getEntityType();
$sets = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection')
    ->setEntityTypeFilter($entityType->getId())
    ->load();

$setId = null;
foreach ($sets as $setInfo) {
    $setId = $setInfo->getId();
    break;
}
if (!$setId) {
    throw new Exception('No attributes sets for product found.');
}

// Create product
$product = Mage::getModel('Magento_Catalog_Model_Product');
$product->setTypeId('simple')
    ->setAttributeSetId($setId)
    ->setWebsiteIds(array(1))
    ->setName('Product 1')
    ->setShortDescription('Product 1 Short Description')
    ->setWeight(1)
    ->setDescription('Product 1 Description')
    ->setSku('product_1')
    ->setPrice(10)
    ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
    ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED)
    ->setTaxClassId(0)
    ->save()
;

$stockItem = Mage::getModel('Magento_CatalogInventory_Model_Stock_Item');
$stockItem->setProductId($product->getId())
    ->setTypeId($product->getTypeId())
    ->setStockId(Magento_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID)
    ->setIsInStock(1)
    ->setQty(10000)
    ->setUseConfigMinQty(1)
    ->setUseConfigBackorders(1)
    ->setUseConfigMinSaleQty(1)
    ->setUseConfigMaxSaleQty(1)
    ->setUseConfigNotifyStockQty(1)
    ->setUseConfigManageStock(1)
    ->setUseConfigQtyIncrements(1)
    ->setUseConfigEnableQtyInc(1)
    ->save()
;
