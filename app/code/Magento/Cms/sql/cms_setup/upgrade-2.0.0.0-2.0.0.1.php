<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Rename column "root_template" into "page_layout"
 */
$connection->changeColumn(
    $installer->getTable('cms_page'),
    'root_template',
    'page_layout',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Page Layout'
    ]
);

$installer->endSetup();
