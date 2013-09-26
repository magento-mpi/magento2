<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup\Migration */
/** @var $this \Magento\Core\Model\Resource\Setup\Generic */
$installer = $this->createMigrationSetup(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('catalogrule', 'conditions_serialized',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace('catalogrule', 'actions_serialized',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
