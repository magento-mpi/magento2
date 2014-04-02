<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Module\Setup */
$installer = $this->createMigrationSetup(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace(
    'salesrule',
    'conditions_serialized',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace(
    'salesrule',
    'actions_serialized',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
