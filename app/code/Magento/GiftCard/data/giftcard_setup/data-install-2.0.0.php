<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->startSetup();
// 0.0.2 => 0.0.3
$installer->addAttribute(
    'catalog_product',
    'giftcard_amounts',
    [
        'group' => 'Advanced Pricing',
        'type' => 'decimal',
        'backend' => 'Magento\GiftCard\Model\Attribute\Backend\Giftcard\Amount',
        'frontend' => '',
        'label' => 'Amounts',
        'input' => 'price',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard',
        'used_in_product_listing' => true,
        'sort_order' => -5
    ]
);

$installer->addAttribute(
    'catalog_product',
    'allow_open_amount',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Allow Open Amount',
        'input' => 'select',
        'input_renderer' => 'Magento\GiftCard\Block\Adminhtml\Renderer\OpenAmount',
        'class' => '',
        'source' => 'Magento\GiftCard\Model\Source\Open',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard',
        'used_in_product_listing' => true,
        'sort_order' => -4
    ]
);
$installer->addAttribute(
    'catalog_product',
    'open_amount_min',
    [
        'group' => 'Advanced Pricing',
        'type' => 'decimal',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
        'frontend' => '',
        'label' => 'Open Amount Min Value',
        'input' => 'price',
        'class' => 'validate-number',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard',
        'used_in_product_listing' => true,
        'sort_order' => -3
    ]
);
$installer->addAttribute(
    'catalog_product',
    'open_amount_max',
    [
        'group' => 'Advanced Pricing',
        'type' => 'decimal',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
        'frontend' => '',
        'label' => 'Open Amount Max Value',
        'input' => 'price',
        'class' => 'validate-number',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard',
        'used_in_product_listing' => true,
        'sort_order' => -2
    ]
);

$installer->addAttribute(
    'catalog_product',
    'giftcard_type',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Card Type',
        'input' => 'select',
        'class' => '',
        'source' => 'Magento\GiftCard\Model\Source\Type',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'required' => true,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'is_redeemable',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Is Redeemable',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'use_config_is_redeemable',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Use Config Is Redeemable',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'lifetime',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Lifetime',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'use_config_lifetime',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Use Config Lifetime',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'email_template',
    [
        'group' => 'Advanced Pricing',
        'type' => 'varchar',
        'backend' => '',
        'frontend' => '',
        'label' => 'Email Template',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'use_config_email_template',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Use Config Email Template',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);
// 0.0.3 => 0.0.4
$installer->addAttribute(
    'catalog_product',
    'allow_message',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Allow Message',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'use_config_allow_message',
    [
        'group' => 'Advanced Pricing',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Use Config Allow Message',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'giftcard'
    ]
);

// 0.0.4 => 0.0.5 make 'weight' attribute applicable to gift card products
$applyTo = $installer->getAttribute('catalog_product', 'weight', 'apply_to');
if ($applyTo) {
    $applyTo = explode(',', $applyTo);
    if (!in_array('giftcard', $applyTo)) {
        $applyTo[] = 'giftcard';
        $installer->updateAttribute('catalog_product', 'weight', 'apply_to', join(',', $applyTo));
    }
}

// 0.0.6 => 0.0.7
$fieldList = ['cost'];

// make these attributes not applicable to gift card products
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (in_array('giftcard', $applyTo)) {
        foreach ($applyTo as $k => $v) {
            if ($v == 'giftcard') {
                unset($applyTo[$k]);
                break;
            }
        }
        $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}

$groupName = 'Product Details';
$entityTypeId = $installer->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $installer->getAttributeSetId($entityTypeId, 'Default');

$attributesOrder = [
    'giftcard_type' => 31,
    'giftcard_amounts' => 32,
    'allow_open_amount' => 33,
    'open_amount_min' => 34,
    'open_amount_max' => 35
];

foreach ($attributesOrder as $key => $order) {
    $attribute = $installer->getAttribute($entityTypeId, $key);
    if ($attribute) {
        $installer->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], $order);
    }
}

$installer->endSetup();
