<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Enterprise_Enterprise_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('enterprise_targetrule', 'conditions_serialized',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED
);
$installer->appendClassAliasReplace('enterprise_targetrule', 'actions_serialized',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED
);
$installer->doUpdateClassAliases();

$installer->endSetup();
