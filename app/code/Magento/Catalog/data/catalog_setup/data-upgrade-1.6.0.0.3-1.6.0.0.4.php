<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp_enabled',
    'source_model',
    'Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled'
);

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp_enabled',
    'default_value',
    \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG
);

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp_display_actual_price_type',
    'source_model',
    'Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price'
);

$installer->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'msrp_display_actual_price_type',
    'default_value',
    \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG
);
