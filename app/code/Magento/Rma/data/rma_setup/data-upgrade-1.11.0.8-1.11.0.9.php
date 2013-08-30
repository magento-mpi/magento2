<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Magento_Enterprise_Model_Resource_Setup_Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_rma_item_eav_attribute', 'data_model',
    Magento_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('attribute_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
