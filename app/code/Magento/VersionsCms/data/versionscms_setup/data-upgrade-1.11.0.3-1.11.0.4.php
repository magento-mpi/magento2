<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Magento_Enterprise_Model_Resource_Setup_Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_versionscms_page_revision', 'content',
    Magento_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('revision_id')
);
$installer->appendClassAliasReplace('magento_versionscms_page_revision', 'layout_update_xml',
    Magento_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->appendClassAliasReplace('magento_versionscms_page_revision', 'custom_layout_update_xml',
    Magento_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
