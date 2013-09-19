<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var Magento_Cms_Model_Resource_Setup $this */

/** @var $installer Magento_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Magento_Core_Model_Resource_Setup_Migration', array('resourceName' =>'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('cms_block', 'content',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('block_id')
);
$installer->appendClassAliasReplace('cms_page', 'content',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('page_id')
);
$installer->appendClassAliasReplace('cms_page', 'layout_update_xml',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);
$installer->appendClassAliasReplace('cms_page', 'custom_layout_update_xml',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();

