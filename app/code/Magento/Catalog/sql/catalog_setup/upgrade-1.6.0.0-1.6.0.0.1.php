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
/** @var $installer Magento_Catalog_Model_Resource_Setup */

$productTypes = array(
    Magento_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Magento_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);

$installer->addAttribute(Magento_Catalog_Model_Product::ENTITY, 'msrp_enabled', array(
    'group'         => 'Prices',
    'backend'       => 'Magento_Catalog_Model_Product_Attribute_Backend_Msrp',
    'frontend'      => '',
    'label'         => 'Apply MAP',
    'input'         => 'select',
    'source'        => 'Magento_Eav_Model_Entity_Attribute_Source_Boolean',
    'global'        => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Msrp_Enabled',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->addAttribute(Magento_Catalog_Model_Product::ENTITY, 'msrp_display_actual_price_type', array(
    'group'         => 'Prices',
    'backend'       => 'Magento_Catalog_Model_Product_Attribute_Backend_Boolean',
    'frontend'      => '',
    'label'         => 'Display Actual Price',
    'input'         => 'select',
    'source'        => 'Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type',
    'global'        => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Msrp_Price',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->addAttribute(Magento_Catalog_Model_Product::ENTITY, 'msrp', array(
    'group'         => 'Prices',
    'backend'       => 'Magento_Catalog_Model_Product_Attribute_Backend_Price',
    'frontend'      => '',
    'label'         => 'Manufacturer\'s Suggested Retail Price',
    'type'          => 'decimal',
    'input'         => 'price',
    'global'        => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true
));
