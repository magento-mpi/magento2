<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Eav\Model\Entity\Setup */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_targetrule',
    'conditions_serialized',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace(
    'magento_targetrule',
    'actions_serialized',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
