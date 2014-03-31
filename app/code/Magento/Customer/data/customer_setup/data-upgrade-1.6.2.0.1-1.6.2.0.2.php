<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Module\Setup */
$installer = $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace(
    'customer_eav_attribute',
    'data_model',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
