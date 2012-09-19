<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Bug MAGETWO-3318 Segmentation Fault */
return;

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('salesrule', 'conditions_serialized',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace('salesrule', 'actions_serialized',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
