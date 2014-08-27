<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Eav\Model\Entity\Setup */
$installer = $this;

$productTypes = [
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
    \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
    \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
];
$productTypes = join(',', $productTypes);

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
        'input_renderer' => 'Magento\Msrp\Block\Adminhtml\Product\Helper\Form\Type',
        'frontend_input_renderer' => 'Magento\Msrp\Block\Adminhtml\Product\Helper\Form\Type',
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
        'source' => 'Magento\Msrp\Model\Product\Attribute\Source\Type\Price',
        'source_model' => 'Magento\Msrp\Model\Product\Attribute\Source\Type\Price',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'default_value' => \Magento\Msrp\Model\Product\Attribute\Source\Type\Price::TYPE_USE_CONFIG,
        'apply_to' => $productTypes,
        'input_renderer' => 'Magento\Msrp\Block\Adminhtml\Product\Helper\Form\Type\Price',
        'frontend_input_renderer' => 'Magento\Msrp\Block\Adminhtml\Product\Helper\Form\Type\Price',
        'visible_on_front' => false,
        'used_in_product_listing' => true
    )
);
