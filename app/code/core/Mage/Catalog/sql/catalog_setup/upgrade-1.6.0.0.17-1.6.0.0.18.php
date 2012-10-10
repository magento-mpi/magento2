<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */
$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'stock_and_qty', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => 'Mage_Catalog_Model_Product_Attribute_Backend_Stock',
    'frontend'          => '',
    'label'             => 'Quantity and Stock',
    'input'             => 'select',
    'class'             => '',
    'input_renderer'    => 'Mage_CatalogInventory_Block_Adminhtml_Form_Field_Stock',
    'source'            => 'Mage_CatalogInventory_Model_Stock_Status',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'           => Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
    'user_defined'      => false,
    'visible'           => true,
    'required'          => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'unique'            => false,
    'is_configurable'   => false,
));
