<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Framework\Module\Setup\Migration */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'cms_block',
    'content',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('block_id')
);
$installer->appendClassAliasReplace(
    'cms_page',
    'content',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('page_id')
);
$installer->appendClassAliasReplace(
    'cms_page',
    'layout_update_xml',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);
$installer->appendClassAliasReplace(
    'cms_page',
    'custom_layout_update_xml',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
