<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup_Migration */
/** @var $this Magento_Core_Model_Resource_Setup_Generic */
$installer = $this->createMigrationSetup(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('catalogrule', 'conditions_serialized',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace('catalogrule', 'actions_serialized',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
