<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'cms_block',
    'content',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('block_id')
);
$installer->appendClassAliasReplace(
    'cms_page',
    'content',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('page_id')
);
$installer->appendClassAliasReplace(
    'cms_page',
    'layout_update_xml',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);
$installer->appendClassAliasReplace(
    'cms_page',
    'custom_layout_update_xml',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
