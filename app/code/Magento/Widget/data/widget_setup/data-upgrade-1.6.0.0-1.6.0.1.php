<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup\Migration */
$installer = \Mage::getResourceModel('Magento\Core\Model\Resource\Setup\Migration', array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('widget_instance', 'instance_type',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('instance_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
