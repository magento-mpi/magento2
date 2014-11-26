<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Eav\Model\Entity\Setup */

$installer = $this;
// add config attributes to catalog product
$installer->addAttribute(
    'catalog_product',
    'related_tgtr_position_limit',
    [
        'label' => 'Related Target Rule Rule Based Positions',
        'visible' => false,
        'user_defined' => false,
        'required' => false,
        'type' => 'int',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'input' => 'text',
        'backend' => 'Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'related_tgtr_position_behavior',
    [
        'label' => 'Related Target Rule Position Behavior',
        'visible' => false,
        'user_defined' => false,
        'required' => false,
        'type' => 'int',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'input' => 'text',
        'backend' => 'Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'upsell_tgtr_position_limit',
    [
        'label' => 'Upsell Target Rule Rule Based Positions',
        'visible' => false,
        'user_defined' => false,
        'required' => false,
        'type' => 'int',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'input' => 'text',
        'backend' => 'Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule'
    ]
);

$installer->addAttribute(
    'catalog_product',
    'upsell_tgtr_position_behavior',
    [
        'label' => 'Upsell Target Rule Position Behavior',
        'visible' => false,
        'user_defined' => false,
        'required' => false,
        'type' => 'int',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
        'input' => 'text',
        'backend' => 'Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule'
    ]
);
