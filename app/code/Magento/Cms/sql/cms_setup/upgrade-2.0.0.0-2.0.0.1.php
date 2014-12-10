<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
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
