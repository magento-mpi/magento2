<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->createMigrationSetup(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace(
    'eav_attribute',
    'attribute_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->appendClassAliasReplace(
    'eav_attribute',
    'backend_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->appendClassAliasReplace(
    'eav_attribute',
    'frontend_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->appendClassAliasReplace(
    'eav_attribute',
    'source_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);

$installer->appendClassAliasReplace(
    'eav_entity_type',
    'entity_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);
$installer->appendClassAliasReplace(
    'eav_entity_type',
    'attribute_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);
$installer->appendClassAliasReplace(
    'eav_entity_type',
    'increment_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);
$installer->appendClassAliasReplace(
    'eav_entity_type',
    'entity_attribute_collection',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_RESOURCE,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
