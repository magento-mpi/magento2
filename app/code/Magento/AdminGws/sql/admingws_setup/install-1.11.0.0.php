<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

/**
 * Resource setup - add columns to roles table:
 * gws_is_all       - yes/no flag
 * gws_websites     - comma-separated
 * gws_store_groups - comma-separated
 */
$tableRoles = $installer->getTable('authorization_role');
$columns = [
    'gws_is_all' => [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'length' => '1',
        'nullable' => false,
        'default' => '1',
        'comment' => 'Yes/No Flag',
    ],
    'gws_websites' => [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => '255',
        'comment' => 'Comma-separated Website Ids',
    ],
    'gws_store_groups' => [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => '255',
        'comment' => 'Comma-separated Store Groups Ids',
    ],
];

$connection = $installer->getConnection();
foreach ($columns as $name => $definition) {
    $connection->addColumn($tableRoles, $name, $definition);
}

$installer->endSetup();
