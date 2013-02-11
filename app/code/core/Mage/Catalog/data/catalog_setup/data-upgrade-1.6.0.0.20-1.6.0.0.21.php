<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$newGeneralTabName = 'Product Details';
$newPriceTabName = 'Advanced Pricing';
$newImagesTabName = 'Image Management';
$newMetaTabName = 'Search Optimization';
$autosettingsTabName = 'Autosettings';
$tabNames = array(
    'General' => array('attribute_group_name' => $newGeneralTabName, 'sort_order' => 1),
    'Images' => array('attribute_group_name' => $newImagesTabName, 'sort_order' => 2),
    'Meta Information' => array('attribute_group_name' => $newMetaTabName, 'sort_order' => 3),
    'Prices' => array('attribute_group_name' => $newPriceTabName, 'sort_order' => 4),
);

$entityTypeId = $this->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

//Rename attribute tabs
foreach ($tabNames as $tabName => $tab) {
    $groupId = $this->getAttributeGroupId($entityTypeId, $attributeSetId, $tabName);
    if ($groupId) {
        foreach ($tab as $propertyName => $propertyValue) {
            $this->updateAttributeGroup($entityTypeId, $attributeSetId, $groupId, $propertyName, $propertyValue);
        }
    }
}

//Add new tab
$this->addAttributeGroup($entityTypeId, $attributeSetId, $autosettingsTabName, 10);

//New attributes order and properties
$properties = array('is_required', 'default_value');
$attributesOrder = array(
    //Product Details tab
    'name' => array($newGeneralTabName => 10),
    'sku' => array($newGeneralTabName => 20),
    'price' => array($newGeneralTabName => 30),
    'tax_class_id' => array($newGeneralTabName => 40, 'is_required' => 0, 'default_value' => 2),
    'image' => array($newGeneralTabName => 50),
    'quantity_and_stock_status' => array($newGeneralTabName => 60, 'default_value' => 1),
    'weight' => array($newGeneralTabName => 70, 'is_required' => 0),
    'category_ids' => array($newGeneralTabName => 80),
    'description' => array($newGeneralTabName => 90, 'is_required' => 0),
    'short_description' => array($newGeneralTabName => 100, 'is_required' => 0),
    'status' => array($newGeneralTabName => 110, 'default_value' => 1),
    //Advanced Pricing tab
    'is_recurring' => array($newPriceTabName => 30),
    'recurring_profile' => array($newPriceTabName => 40),
    //Autosettings tab
    'url_key' => array($autosettingsTabName => 10),
    'visibility' => array($autosettingsTabName => 20, 'is_required' => 0),
    'news_to_date' => array($autosettingsTabName => 30),
    'news_from_date' => array($autosettingsTabName => 40),
    'country_of_manufacture' => array($autosettingsTabName => 50),
    'gift_message_available' => array($autosettingsTabName => 60),
);

foreach ($attributesOrder as $key => $value) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        foreach ($value as $propertyName => $propertyValue) {
            if (in_array($propertyName, $properties)) {
                $this->updateAttribute(
                    $entityTypeId,
                    $attribute['attribute_id'],
                    $propertyName,
                    $propertyValue
                );
            } else {
                $this->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $propertyName,
                    $attribute['attribute_id'],
                    $propertyValue
                );
            }
        }
    }
}

//Delete attribute group
$this->removeAttributeGroup($entityTypeId, $attributeSetId, 'Recurring Profile');
$this->removeAttributeGroup($entityTypeId, $attributeSetId, 'Gift Options');
