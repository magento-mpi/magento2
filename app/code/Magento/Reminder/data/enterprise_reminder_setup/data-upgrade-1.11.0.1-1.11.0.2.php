<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Magento_Enterprise_Model_Resource_Setup_Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_reminder_rule', 'conditions_serialized',
    Magento_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
