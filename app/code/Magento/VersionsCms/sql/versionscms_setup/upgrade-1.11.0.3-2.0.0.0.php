<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Rename column "root_template" into "page_layout"
 */
$connection->changeColumn(
    $installer->getTable('magento_versionscms_page_revision'),
    'root_template',
    'page_layout',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Page Layout'
    ]
);

/**
 * Rename column "custom_root_template" into "custom_page_layout"
 */
$connection->changeColumn(
    $installer->getTable('magento_versionscms_page_revision'),
    'custom_root_template',
    'custom_page_layout',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Custom Page Layout'
    ]
);

$installer->endSetup();
