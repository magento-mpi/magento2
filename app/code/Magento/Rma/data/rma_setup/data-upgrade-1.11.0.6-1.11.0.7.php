<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Rma\Model\Resource\Setup */
// Add Product's Attribute
$installer = $this->getCatalogSetup(array('resourceName' => 'catalog_setup'));

$installer->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_returnable');
$installer->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'use_config_is_returnable');

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'is_returnable',
    array(
        'group' => 'Autosettings',
        'frontend' => '',
        'label' => 'Enable RMA',
        'input' => 'select',
        'class' => '',
        'source' => 'Magento\Rma\Model\Product\Source',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => \Magento\Rma\Model\Product\Source::ATTRIBUTE_ENABLE_RMA_USE_CONFIG,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => implode(',', $this->getRefundableProducts()),
        'input_renderer' => 'Magento\Rma\Block\Adminhtml\Product\Renderer'
    )
);
