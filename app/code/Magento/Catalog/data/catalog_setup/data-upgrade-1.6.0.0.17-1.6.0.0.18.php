<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$attribute = $this->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'category_ids');
if ($attribute) {
    $properties = array(
        'sort_order' => 9,
        'is_visible' => true,
        'frontend_label' => 'Categories',
        'input' => 'categories',
        'group' => 'General Information',
        'backend_model' => 'Magento\Catalog\Model\Product\Attribute\Backend\Category',
        'frontend_input_renderer' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category'
    );
    foreach ($properties as $key => $value) {
        $this->updateAttribute($attribute['entity_type_id'], $attribute['attribute_id'], $key, $value);
    }
}
