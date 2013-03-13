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
    $this->addAttributeToSet(
        $attribute['entity_type_id'],
        $this->getAttributeSetId($attribute['entity_type_id'], 'Minimal'),
        $this->getGeneralGroupName(),
        $attribute['attribute_id']
    );
    $properties = array(
        'sort_order' => 9,
        'is_visible' => true,
        'frontend_label' => 'Categories',
        'input' => 'categories',
        'group' => 'General Information',
        'backend_model' => 'Mage_Catalog_Model_Product_Attribute_Backend_Category',
        'frontend_input_renderer' => 'Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Category',
    );
    foreach ($properties as $key => $value) {
        $this->updateAttribute(
            $attribute['entity_type_id'],
            $attribute['attribute_id'],
            $key,
            $value
        );
    }
}
