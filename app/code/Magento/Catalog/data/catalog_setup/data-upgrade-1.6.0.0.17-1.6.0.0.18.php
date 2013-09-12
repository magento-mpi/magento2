<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Catalog_Model_Resource_Setup */

$attribute = $this->getAttribute(Magento_Catalog_Model_Product::ENTITY, 'category_ids');
if ($attribute) {
    $properties = array(
        'sort_order' => 9,
        'is_visible' => true,
        'frontend_label' => 'Categories',
        'input' => 'categories',
        'group' => 'General Information',
        'backend_model' => 'Magento_Catalog_Model_Product_Attribute_Backend_Category',
        'frontend_input_renderer' => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Category',
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
