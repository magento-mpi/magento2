<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$fields = array('conditions_serialized', 'actions_serialized');

foreach ($fields as $field) {
    $installer->appendClassAliasReplace('salesrule', $field,
        Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
        Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED
    );
}

$installer->doUpdateClassAliases();

$installer->endSetup();
