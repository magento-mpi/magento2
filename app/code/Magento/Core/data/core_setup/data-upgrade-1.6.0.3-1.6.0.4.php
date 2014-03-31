<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace(
    'core_config_data',
    'value',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('config_id')
);
$installer->appendClassAliasReplace(
    'core_layout_update',
    'xml',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('layout_update_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
