<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Setup\Module\SetupModule */
/** @var $connection \Magento\Framework\DB\Adapter\Pdo\Mysql */
$connection = $this->getConnection();

$connection->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index'),
    $this->getFkName('magento_catalogpermissions_index', 'customer_group_id', 'customer_group', 'customer_group_id')
)->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index'),
    $this->getFkName('magento_catalogpermissions_index', 'category_id', 'catalog_category_entity', 'entity_id')
)->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index'),
    $this->getFkName('magento_catalogpermissions_index', 'website_id', 'store_website', 'website_id')
);

$table = $connection->newTable(
    $this->getTable('magento_catalogpermissions_index_tmp')
)->addColumn(
    'category_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Category Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Website Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Customer Group Id'
)->addColumn(
    'grant_catalog_category_view',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Grant Catalog Category View'
)->addColumn(
    'grant_catalog_product_price',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Grant Catalog Product Price'
)->addColumn(
    'grant_checkout_items',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Grant Checkout Items'
)->addIndex(
    $this->getIdxName('magento_catalogpermissions_index', array('website_id')),
    array('website_id')
)->addIndex(
    $this->getIdxName('magento_catalogpermissions_index', array('customer_group_id')),
    array('customer_group_id')
)->setComment(
    'Catalog Category Permissions Temporary Index'
);

$connection->createTable($table);
