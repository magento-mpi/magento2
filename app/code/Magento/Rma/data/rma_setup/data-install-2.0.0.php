<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Rma\Model\Resource\Setup */

/**
 * Prepare database before module installation
 */
$this->installEntities();
$this->installForms();

//Add Product's Attribute
/** @var \Magento\Catalog\Model\Resource\Setup $this */
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

$installer->addAttribute(
    'rma_item',
    'qty_returned',
    array(
        'type' => 'static',
        'label' => 'Qty of returned items',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 45,
        'position' => 45
    )
);

$installer->addAttribute(
    'rma_item',
    'product_admin_name',
    array(
        'type' => 'static',
        'label' => 'Product Name For Backend',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 46,
        'position' => 46
    )
);
$installer->addAttribute(
    'rma_item',
    'product_admin_sku',
    array(
        'type' => 'static',
        'label' => 'Product Sku For Backend',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 47,
        'position' => 47
    )
);
$installer->addAttribute(
    'rma_item',
    'product_options',
    array(
        'type' => 'static',
        'label' => 'Product Options',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 48,
        'position' => 48
    )
);

/* setting is_qty_decimal field in rma_item_entity table as a static attribute */
$installer->addAttribute(
    'rma_item',
    'is_qty_decimal',
    array(
        'type' => 'static',
        'label' => 'Is item quantity decimal',
        'input' => 'text',
        'visible' => false,
        'sort_order' => 15,
        'position' => 15
    )
);

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

/** @var $installer \Magento\Framework\Module\Setup\Migration */
$installer = $this->createMigrationSetup();

$installer->appendClassAliasReplace(
    'magento_rma_item_eav_attribute',
    'data_model',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$groupName = 'Autosettings';
$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attribute = $this->getAttribute($entityTypeId, 'is_returnable');
if ($attribute) {
    $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], 90);
}

$installer->endSetup();
