<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Enterprise_Enterprise_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('enterprise_reminder_rule', 'conditions_serialized',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED
);
$installer->doUpdateClassAliases();

$installer->endSetup();
