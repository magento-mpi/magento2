<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$productTypes = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
    Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
);
$productTypes = join(',', $productTypes);

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_enabled', array(
    'group'         => 'Prices',
    'backend'       => 'Mage_Catalog_Model_Product_Attribute_Backend_Msrp',
    'frontend'      => '',
    'label'         => 'Apply MAP',
    'input'         => 'select',
    'source'        => 'Mage_Eav_Model_Entity_Attribute_Source_Boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Msrp_Enabled',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp_display_actual_price_type', array(
    'group'         => 'Prices',
    'backend'       => 'Mage_Catalog_Model_Product_Attribute_Backend_Boolean',
    'frontend'      => '',
    'label'         => 'Display Actual Price',
    'input'         => 'select',
    'source'        => 'Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'default'       => '',
    'apply_to'      => $productTypes,
    'input_renderer'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Msrp_Price',
    'visible_on_front' => false,
    'used_in_product_listing' => true
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'msrp', array(
    'group'         => 'Prices',
    'backend'       => 'Mage_Catalog_Model_Product_Attribute_Backend_Price',
    'frontend'      => '',
    'label'         => 'Manufacturer\'s Suggested Retail Price',
    'type'          => 'decimal',
    'input'         => 'price',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'apply_to'      => $productTypes,
    'visible_on_front' => false,
    'used_in_product_listing' => true
));
