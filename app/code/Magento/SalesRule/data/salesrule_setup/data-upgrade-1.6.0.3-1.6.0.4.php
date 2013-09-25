<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Sales_Model_Resource_Setup */
/** @var $installer Magento_Core_Model_Resource_Setup_Migration */
$installer = $this->getMigrationSetup(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('salesrule', 'conditions_serialized',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace('salesrule', 'actions_serialized',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
