<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$newGeneralTabName = 'Product Details';
$newPriceTabName = 'Advanced Pricing';
$newImagesTabName = 'Image Management';
$newMetaTabName = 'Search Optimization';
$autosettingsTabName = 'Autosettings';
$tabNames = array(
    'General' => array(
        'attribute_group_name' => $newGeneralTabName,
        'attribute_group_code' => preg_replace('/[^a-z0-9]+/', '-', strtolower($newGeneralTabName)),
        'tab_group_code' => 'basic',
        'sort_order' => 10
    ),
    'Images' => array(
        'attribute_group_name' => $newImagesTabName,
        'attribute_group_code' => preg_replace('/[^a-z0-9]+/', '-', strtolower($newImagesTabName)),
        'tab_group_code' => 'basic',
        'sort_order' => 20
    ),
    'Meta Information' => array(
        'attribute_group_name' => $newMetaTabName,
        'attribute_group_code' => preg_replace('/[^a-z0-9]+/', '-', strtolower($newMetaTabName)),
        'tab_group_code' => 'basic',
        'sort_order' => 30
    ),
    'Prices' => array(
        'attribute_group_name' => $newPriceTabName,
        'attribute_group_code' => preg_replace('/[^a-z0-9]+/', '-', strtolower($newPriceTabName)),
        'tab_group_code' => 'advanced',
        'sort_order' => 40
    ),
    'Design' => array('attribute_group_code' => 'design', 'tab_group_code' => 'advanced', 'sort_order' => 50)
);

$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
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
$this->addAttributeGroup($entityTypeId, $attributeSetId, $autosettingsTabName, 60);
$this->updateAttributeGroup($entityTypeId, $attributeSetId, 'Autosettings', 'attribute_group_code', 'autosettings');
$this->updateAttributeGroup($entityTypeId, $attributeSetId, 'Autosettings', 'tab_group_code', 'advanced');

//New attributes order and properties
$properties = array('is_required', 'default_value', 'frontend_input_renderer');
$attributesOrder = array(
    //Product Details tab
    'name' => array($newGeneralTabName => 10),
    'sku' => array($newGeneralTabName => 20),
    'price' => array($newGeneralTabName => 30),
    'tax_class_id' => array($newGeneralTabName => 40, 'is_required' => 0, 'default_value' => 2),
    'image' => array($newGeneralTabName => 50),
    'weight' => array($newGeneralTabName => 70, 'is_required' => 0),
    'category_ids' => array($newGeneralTabName => 80),
    'description' => array($newGeneralTabName => 90, 'is_required' => 0),
    'status' => array(
        $newGeneralTabName => 100,
        'is_required' => 0,
        'default_value' => 1,
        'frontend_input_renderer' => 'Magento\Data\Form\Element\Hidden'
    ),
    //Autosettings tab
    'short_description' => array($autosettingsTabName => 0, 'is_required' => 0),
    'url_key' => array($autosettingsTabName => 10),
    'visibility' => array($autosettingsTabName => 20, 'is_required' => 0),
    'news_from_date' => array($autosettingsTabName => 30),
    'news_to_date' => array($autosettingsTabName => 40),
    'country_of_manufacture' => array($autosettingsTabName => 50)
);

foreach ($attributesOrder as $key => $value) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        foreach ($value as $propertyName => $propertyValue) {
            if (in_array($propertyName, $properties)) {
                $this->updateAttribute($entityTypeId, $attribute['attribute_id'], $propertyName, $propertyValue);
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
