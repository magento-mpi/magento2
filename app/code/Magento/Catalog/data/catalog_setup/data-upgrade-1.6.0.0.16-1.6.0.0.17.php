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

/** @var $installer \Magento\Core\Model\Resource\Setup\Migration */
$installer = $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
$installer->startSetup();

$attributeData = $this->getAttribute('catalog_category', 'custom_layout_update');
$installer->appendClassAliasReplace('catalog_category_entity_text', 'value',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('value_id'),
    'attribute_id = ' . (int) $attributeData['attribute_id']
);

$attributeData = $this->getAttribute('catalog_product', 'custom_layout_update');
$installer->appendClassAliasReplace('catalog_product_entity_text', 'value',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('value_id'),
    'attribute_id = ' . (int) $attributeData['attribute_id']
);

$installer->appendClassAliasReplace('catalog_eav_attribute', 'frontend_input_renderer',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
