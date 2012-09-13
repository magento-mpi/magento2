<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Bug MAGETWO-3318 Segmentation Fault */
return;

/** @var $installer Enterprise_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Enterprise_Enterprise_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('enterprise_cms_page_revision', 'content',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('revision_id')
);
$installer->appendClassAliasReplace('enterprise_cms_page_revision', 'layout_update_xml',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->appendClassAliasReplace('enterprise_cms_page_revision', 'custom_layout_update_xml',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
