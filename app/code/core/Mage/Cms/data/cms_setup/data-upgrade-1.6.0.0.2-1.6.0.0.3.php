<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Bug MAGETWO-3318 Segmentation Fault */
return;

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('cms_block', 'content',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('block_id')
);
$installer->appendClassAliasReplace('cms_page', 'content',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('page_id')
);
$installer->appendClassAliasReplace('cms_page', 'layout_update_xml',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);
$installer->appendClassAliasReplace('cms_page', 'custom_layout_update_xml',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();

