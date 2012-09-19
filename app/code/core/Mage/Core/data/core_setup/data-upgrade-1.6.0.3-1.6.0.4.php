<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Bug MAGETWO-3318 Segmentation Fault */
return;

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('core_config_data', 'value',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('config_id')
);
$installer->appendClassAliasReplace('core_layout_update', 'xml',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('layout_update_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
