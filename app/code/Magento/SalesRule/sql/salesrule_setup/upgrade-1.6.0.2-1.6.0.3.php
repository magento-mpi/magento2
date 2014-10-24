<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$connection = $installer->getConnection();

$rulesTable = $installer->getTable('salesrule');
$websitesTable = $installer->getTable('store_website');
$customerGroupsTable = $installer->getTable('customer_group');
$rulesWebsitesTable = $installer->getTable('salesrule_website');
$rulesCustomerGroupsTable = $installer->getTable('salesrule_customer_group');

$installer->startSetup();
/**
 * Create table 'salesrule_website' if not exists. This table will be used instead of
 * column website_ids of main catalog rules table
 */
$table = $connection->newTable(
    $rulesWebsitesTable
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Website Id'
)->addIndex(
    $installer->getIdxName('salesrule_website', array('website_id')),
    array('website_id')
)->addForeignKey(
    $installer->getFkName('salesrule_website', 'rule_id', 'salesrule', 'rule_id'),
    'rule_id',
    $rulesTable,
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('salesrule_website', 'website_id', 'core/website', 'website_id'),
    'website_id',
    $websitesTable,
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Sales Rules To Websites Relations'
);

$connection->createTable($table);


/**
 * Create table 'salesrule_customer_group' if not exists. This table will be used instead of
 * column customer_group_ids of main catalog rules table
 */
$table = $connection->newTable(
    $rulesCustomerGroupsTable
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Customer Group Id'
)->addIndex(
    $installer->getIdxName('salesrule_customer_group', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName('salesrule_customer_group', 'rule_id', 'salesrule', 'rule_id'),
    'rule_id',
    $rulesTable,
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('salesrule_customer_group', 'customer_group_id', 'customer_group', 'customer_group_id'),
    'customer_group_id',
    $customerGroupsTable,
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Sales Rules To Customer Groups Relations'
);

$connection->createTable($table);


/**
 * Change default value to "null" for "from" and "to" dates columns
 */
$connection->modifyColumn(
    $rulesTable,
    'from_date',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 'nullable' => true, 'default' => null)
);

$connection->modifyColumn(
    $rulesTable,
    'to_date',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 'nullable' => true, 'default' => null)
);

$installer->endSetup();
