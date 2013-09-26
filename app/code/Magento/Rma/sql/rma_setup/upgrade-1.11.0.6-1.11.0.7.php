<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Rma\Model\Resource\Setup */
//Add Product's Attribute
$installer = $this->getCatalogResourceSetup(array('resourceName' => 'catalog_setup'));

$installer->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_returnable');
$installer->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'use_config_is_returnable');

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_returnable', array(
    'group'             => 'General',
    'frontend'          => '',
    'label'             => 'Enable RMA',
    'input'             => 'select',
    'class'             => '',
    'source'            => 'Magento\Rma\Model\Product\Source',
    'global'            => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => \Magento\Rma\Model\Product\Source::ATTRIBUTE_ENABLE_RMA_USE_CONFIG,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          =>
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE . ',' .
        \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE . ',' .
        \Magento\Catalog\Model\Product\Type::TYPE_GROUPED . ',' .
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    'is_configurable'   => false,
    'input_renderer'    => 'Magento\Rma\Block\Adminhtml\Product\Renderer',
));
