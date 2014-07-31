<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$productTypes = array(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
    \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp_enabled',
    array(
        'group' => 'Prices',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Msrp',
        'frontend' => '',
        'label' => 'Apply MAP',
        'input' => 'select',
        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'apply_to' => $productTypes,
        'input_renderer' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Msrp\Enabled',
        'visible_on_front' => false,
        'used_in_product_listing' => true
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp_display_actual_price_type',
    array(
        'group' => 'Prices',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
        'frontend' => '',
        'label' => 'Display Actual Price',
        'input' => 'select',
        'source' => 'Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'apply_to' => $productTypes,
        'input_renderer' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Msrp\Price',
        'visible_on_front' => false,
        'used_in_product_listing' => true
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp',
    array(
        'group' => 'Prices',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
        'frontend' => '',
        'label' => 'Manufacturer\'s Suggested Retail Price',
        'type' => 'decimal',
        'input' => 'price',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'apply_to' => $productTypes,
        'visible_on_front' => false,
        'used_in_product_listing' => true
    )
);
