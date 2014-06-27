<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */

$fieldList = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'cost',
    'tier_price',
    'weight'
);
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to'));
    if (!in_array('bundle', $applyTo)) {
        $applyTo[] = 'bundle';
        $installer->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $field,
            'apply_to',
            implode(',', $applyTo)
        );
    }
}

$applyTo = explode(',', $installer->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cost', 'apply_to'));
unset($applyTo[array_search('bundle', $applyTo)]);
$installer->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'cost', 'apply_to', implode(',', $applyTo));

/**
 * Add attributes to the eav/attribute
 */
$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'price_type',
    array(
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => '',
        'input' => '',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'visible' => false,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => 'bundle'
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'sku_type',
    array(
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => '',
        'input' => '',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'visible' => false,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'bundle'
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'weight_type',
    array(
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => '',
        'input' => '',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'visible' => false,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => 'bundle'
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'price_view',
    array(
        'group' => 'Prices',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Price View',
        'input' => 'select',
        'class' => '',
        'source' => 'Magento\Bundle\Model\Product\Attribute\Source\Price\View',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => 'bundle'
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'shipment_type',
    array(
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Shipment',
        'input' => '',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'visible' => false,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => 'bundle'
    )
);
