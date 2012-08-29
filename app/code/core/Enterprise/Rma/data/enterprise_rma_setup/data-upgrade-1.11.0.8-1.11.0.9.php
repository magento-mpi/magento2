<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('enterprise_rma_item_eav_attribute', 'data_model',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN
);
$installer->doUpdateClassAliases();

$installer->endSetup();
