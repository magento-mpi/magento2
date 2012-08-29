<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$tables = array(
    'core_config_data' => array(
        array(
            'name'         => 'value',
            'entity_type'  => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
            'content_type' => Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
        ),
    ),
    'core_layout_update' => array(
        array(
            'name'         => 'xml',
            'entity_type'  => Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
            'content_type' => Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
        ),
    ),
);

foreach ($tables as $table => $fields) {
    foreach ($fields as $fieldData) {
        $installer->appendClassAliasReplace(
            $table,
            $fieldData['name'],
            $fieldData['entity_type'],
            $fieldData['content_type']
        );
    }
}

$installer->doUpdateClassAliases();

$installer->endSetup();
