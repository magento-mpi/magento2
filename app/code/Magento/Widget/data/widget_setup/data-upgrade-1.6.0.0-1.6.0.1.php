<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Widget_Model_Resource_Setup */
/** @var $installer Magento_Core_Model_Resource_Setup_Migration */
$installer = $this->getMigrationInstance(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('widget_instance', 'instance_type',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('instance_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
