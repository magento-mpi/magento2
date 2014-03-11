<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Catalog\Model\Resource\Setup $this */
/** @var \Magento\DB\Adapter\AdapterInterface $connection */
$connection = $this->getConnection();

$connection
    ->dropForeignKey(
        $this->getTable('magento_catalogpermissions_index'),
        $this->getFkName('magento_catalogpermissions_index', 'customer_group_id', 'customer_group', 'customer_group_id')
    )
    ->dropForeignKey(
        $this->getTable('magento_catalogpermissions_index'),
        $this->getFkName('magento_catalogpermissions_index', 'category_id', 'catalog_category_entity', 'entity_id')
    )
    ->dropForeignKey(
        $this->getTable('magento_catalogpermissions_index'),
        $this->getFkName('magento_catalogpermissions_index', 'website_id', 'core_website', 'website_id')
    );

$table = $connection->newTable($this->getTable('magento_catalogpermissions_index_tmp'))
    ->addColumn(
        'category_id',
        \Magento\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned'  => true, 'nullable' => false],
        'Category Id'
    )
    ->addColumn(
        'website_id',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned'  => true, 'nullable' => false],
        'Website Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned'  => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'grant_catalog_category_view',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Category View'
    )
    ->addColumn(
        'grant_catalog_product_price',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Product Price'
    )
    ->addColumn(
        'grant_checkout_items',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Checkout Items'
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index', ['category_id']),
        ['category_id']
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index', ['customer_group_id']),
        ['customer_group_id']
    )
    ->setComment('Catalog Category Permissions Temporary Index');

$connection->createTable($table);
