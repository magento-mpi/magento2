<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_versionscms_page_revision',
    'content',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('revision_id')
);
$installer->appendClassAliasReplace(
    'magento_versionscms_page_revision',
    'layout_update_xml',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->appendClassAliasReplace(
    'magento_versionscms_page_revision',
    'custom_layout_update_xml',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
