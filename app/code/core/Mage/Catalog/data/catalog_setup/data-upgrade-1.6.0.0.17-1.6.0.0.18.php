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

$attribute = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'category_ids');

if ($attribute) {
    $properties = array(
        'sort_order' => 5,
        'is_visible' => true,
        'label' => 'Categories',
        'input' => 'categories',
        'group' => 'General Information',
        'backend_model' => 'Mage_Catalog_Model_Product_Attribute_Backend_Category'
    );

    foreach ($properties as $key => $value) {
        $installer->updateAttribute(
            $attribute['entity_type_id'],
            $attribute['attribute_id'],
            $key,
            $value
        );
    }
}