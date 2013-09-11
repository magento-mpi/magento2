<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */

$productTypes = array(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
    \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'msrp_enabled', array(
    'group'         => 'Prices',
    'backend'       => '\Magento\Catalog\Model\Product\Attribute\Backend\Msrp',
    'frontend'      => '',
    'label'         => 'Apply MAP',
    'input'         => 'select',
    'source'        => '\Magento\Eav\Model\Entity\Attribute\Source\Boolean',
    'global'        => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => '\Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Msrp\Enabled',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'msrp_display_actual_price_type', array(
    'group'         => 'Prices',
    'backend'       => '\Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
    'frontend'      => '',
    'label'         => 'Display Actual Price',
    'input'         => 'select',
    'source'        => '\Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type',
    'global'        => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => '\Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Msrp\Price',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'msrp', array(
    'group'         => 'Prices',
    'backend'       => '\Magento\Catalog\Model\Product\Attribute\Backend\Price',
    'frontend'      => '',
    'label'         => 'Manufacturer\'s Suggested Retail Price',
    'type'          => 'decimal',
    'input'         => 'price',
    'global'        => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true
));
