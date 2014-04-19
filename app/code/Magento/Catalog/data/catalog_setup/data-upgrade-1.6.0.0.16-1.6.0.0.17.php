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

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$attributeData = $this->getAttribute('catalog_category', 'custom_layout_update');
$installer->appendClassAliasReplace(
    'catalog_category_entity_text',
    'value',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('value_id'),
    'attribute_id = ' . (int)$attributeData['attribute_id']
);

$attributeData = $this->getAttribute('catalog_product', 'custom_layout_update');
$installer->appendClassAliasReplace(
    'catalog_product_entity_text',
    'value',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('value_id'),
    'attribute_id = ' . (int)$attributeData['attribute_id']
);

$installer->appendClassAliasReplace(
    'catalog_eav_attribute',
    'frontend_input_renderer',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
