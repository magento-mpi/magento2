<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Catalog_Model_Resource_Setup */
$this->addAttribute(Magento_Catalog_Model_Product::ENTITY, 'quantity_and_stock_status', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => 'Magento_Catalog_Model_Product_Attribute_Backend_Stock',
    'frontend'          => '',
    'label'             => 'Quantity',
    'input'             => 'select',
    'class'             => '',
    'input_renderer'    => 'Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock',
    'source'            => 'Magento_CatalogInventory_Model_Stock_Status',
    'global'            => Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default'           => Magento_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
    'user_defined'      => false,
    'visible'           => true,
    'required'          => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'unique'            => false,
    'is_configurable'   => false,
));
