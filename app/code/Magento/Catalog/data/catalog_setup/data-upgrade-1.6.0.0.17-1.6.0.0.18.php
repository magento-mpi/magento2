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

$this->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'quantity_and_stock_status',
    array(
        'group' => 'General',
        'type' => 'int',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Stock',
        'frontend' => '',
        'label' => 'Quantity',
        'input' => 'select',
        'class' => '',
        'input_renderer' => 'Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock',
        'source' => 'Magento\CatalogInventory\Model\Stock\Status',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'default' => \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK,
        'user_defined' => false,
        'visible' => true,
        'required' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'unique' => false
    )
);
