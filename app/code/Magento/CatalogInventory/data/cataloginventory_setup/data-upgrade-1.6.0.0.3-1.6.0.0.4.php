<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Catalog_Model_Resource_Setup */

$groupName = 'Product Details';
$entityTypeId = $this->getEntityTypeId(Magento_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attribute = $this->getAttribute($entityTypeId, 'quantity_and_stock_status');
if ($attribute) {
    $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], 60);
    $this->updateAttribute($entityTypeId, $attribute['attribute_id'], 'default_value', 1);
}
