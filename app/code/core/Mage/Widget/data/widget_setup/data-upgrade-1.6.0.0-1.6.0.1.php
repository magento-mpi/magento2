<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Bug MAGETWO-3318 Segmentation Fault */
return;

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('widget_instance', 'instance_type',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('instance_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
