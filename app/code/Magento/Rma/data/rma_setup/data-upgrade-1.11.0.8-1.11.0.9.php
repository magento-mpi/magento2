<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Enterprise\Model\Resource\Setup\Migration */
$installer = \Mage::getResourceModel('\Magento\Enterprise\Model\Resource\Setup\Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_rma_item_eav_attribute', 'data_model',
    \Magento\Enterprise\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Enterprise\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
