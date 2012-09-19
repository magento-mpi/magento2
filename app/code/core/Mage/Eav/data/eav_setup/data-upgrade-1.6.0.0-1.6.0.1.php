<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Bug MAGETWO-3318 Segmentation Fault */
return;

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('eav_attribute', 'attribute_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->appendClassAliasReplace('eav_attribute', 'backend_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->appendClassAliasReplace('eav_attribute', 'frontend_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->appendClassAliasReplace('eav_attribute', 'source_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);

$installer->appendClassAliasReplace('eav_entity_type', 'entity_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);
$installer->appendClassAliasReplace('eav_entity_type', 'attribute_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);
$installer->appendClassAliasReplace('eav_entity_type', 'increment_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);
$installer->appendClassAliasReplace('eav_entity_type', 'entity_attribute_collection',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('entity_type_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
