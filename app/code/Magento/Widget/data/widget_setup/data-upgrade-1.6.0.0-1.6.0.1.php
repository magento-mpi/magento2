<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Magento_Core_Model_Resource_Setup_Migration', array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('widget_instance', 'instance_type',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('instance_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
