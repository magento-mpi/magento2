<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\VersionsCms\Model\Resource\Setup */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_versionscms_page_revision',
    'content',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('revision_id')
);
$installer->appendClassAliasReplace(
    'magento_versionscms_page_revision',
    'layout_update_xml',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->appendClassAliasReplace(
    'magento_versionscms_page_revision',
    'custom_layout_update_xml',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
