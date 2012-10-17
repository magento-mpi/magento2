<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('catalogrule', 'conditions_serialized',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace('catalogrule', 'actions_serialized',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
