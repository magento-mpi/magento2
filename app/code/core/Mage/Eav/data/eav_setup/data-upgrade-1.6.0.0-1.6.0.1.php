<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$tables = array(
    'eav_attribute' => array(
        'attribute_model' => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        'backend_model'   => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        'frontend_model'  => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        'source_model'    => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    ),
    'eav_entity_type' => array(
        'entity_model'                => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        'attribute_model'             => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        'increment_model'             => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        'entity_attribute_collection' => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE,
    ),
);

foreach ($tables as $table => $fields) {
    foreach ($fields as $field => $entityType) {
        $installer->appendClassAliasReplace($table, $field, $entityType,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN
        );
    }
}

$installer->doUpdateClassAliases();

$installer->endSetup();
