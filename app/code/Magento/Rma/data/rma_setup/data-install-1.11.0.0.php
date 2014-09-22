<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Rma\Model\Resource\Setup */
/** @var $installer \Magento\Rma\Model\Resource\Setup */
$installer = $this;

/**
 * Prepare database before module installation
 */
$installer->installEntities();
$installer->installForms();

//Add Product's Attribute
/** @var \Magento\Catalog\Model\Resource\Setup $installer */
$installer = $this->getCatalogSetup(array('resourceName' => 'catalog_setup'));

/**
 * Prepare database before module installation
 */
$installer->startSetup();

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'is_returnable',
    array(
        'group' => 'Autosettings',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Enable RMA',
        'input' => 'select',
        'class' => '',
        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '1',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => implode($this->getRefundableProducts(), ','),
        'input_renderer' => 'Magento\Rma\Block\Adminhtml\Product\Renderer'
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'use_config_is_returnable',
    array(
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Use Config Enable RMA',
        'input' => 'text',
        'class' => '',
        'source' => '',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'default' => '1',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => implode($this->getRefundableProducts(), ',')
    )
);

$installer->endSetup();
