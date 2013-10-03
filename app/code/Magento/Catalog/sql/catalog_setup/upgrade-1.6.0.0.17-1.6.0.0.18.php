<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */
$this->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'quantity_and_stock_status', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => 'Magento\Catalog\Model\Product\Attribute\Backend\Stock',
    'frontend'          => '',
    'label'             => 'Quantity',
    'input'             => 'select',
    'class'             => '',
    'input_renderer'    => 'Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock',
    'source'            => 'Magento\CatalogInventory\Model\Stock\Status',
    'global'            => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
    'default'           => \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK,
    'user_defined'      => false,
    'visible'           => true,
    'required'          => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'unique'            => false,
    'is_configurable'   => false,
));
