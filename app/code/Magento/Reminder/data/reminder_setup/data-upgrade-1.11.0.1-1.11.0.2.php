<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Framework\Module\Setup */
/** @var $installer \Magento\Framework\Module\Setup\Migration */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_reminder_rule',
    'conditions_serialized',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
