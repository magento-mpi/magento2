<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

//Add Product's Attribute
$installer = Mage::getResourceModel('Mage_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'));

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_returnable');
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'use_config_is_returnable');

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'is_returnable', array(
    'group'             => 'General',
    'frontend'          => '',
    'label'             => 'Enable RMA',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'Enterprise_Rma_Model_Product_Source',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => Enterprise_Rma_Model_Product_Source::ATTRIBUTE_ENABLE_RMA_USE_CONFIG,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          =>
        Mage_Catalog_Model_Product_Type::TYPE_SIMPLE . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED . ',' .
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
    'is_configurable'   => false,
    'input_renderer'    => 'Enterprise_Rma_Block_Adminhtml_Product_Renderer',
));
