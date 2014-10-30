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

/**
 * Create table 'magento_catalogpermissions'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogpermissions'))
    ->addColumn(
        'permission_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Permission Id'
    )
    ->addColumn(
        'category_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Category Id'
    )
    ->addColumn(
        'website_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true],
        'Website Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true],
        'Customer Group Id'
    )
    ->addColumn(
        'grant_catalog_category_view',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false],
        'Grant Catalog Category View'
    )
    ->addColumn(
        'grant_catalog_product_price',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false],
        'Grant Catalog Product Price'
    )
    ->addColumn(
        'grant_checkout_items',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false],
        'Grant Checkout Items'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_catalogpermissions',
            ['category_id', 'website_id', 'customer_group_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['category_id', 'website_id', 'customer_group_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogpermissions', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogpermissions', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_catalogpermissions', 'category_id', 'catalog_category_entity', 'entity_id'),
        'category_id',
        $installer->getTable('catalog_category_entity'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_catalogpermissions', 'customer_group_id', 'customer_group', 'customer_group_id'),
        'customer_group_id',
        $installer->getTable('customer_group'),
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_catalogpermissions', 'website_id', 'store_website', 'website_id'),
        'website_id',
        $installer->getTable('store_website'),
        'website_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Catalogpermissions');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogpermissions_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogpermissions_index'))
    ->addColumn(
        'category_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Category Id'
    )
    ->addColumn(
        'website_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Website Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'grant_catalog_category_view',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Category View'
    )
    ->addColumn(
        'grant_catalog_product_price',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Product Price'
    )
    ->addColumn(
        'grant_checkout_items',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Checkout Items'
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogpermissions_index', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogpermissions_index', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_catalogpermissions_index',
            ['category_id', 'website_id', 'customer_group_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['category_id', 'website_id', 'customer_group_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]

    )
    ->setComment('Enterprise Catalogpermissions Index');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogpermissions_index_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogpermissions_index_product'))
    ->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Product Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'grant_catalog_category_view',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Category View'
    )
    ->addColumn(
        'grant_catalog_product_price',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Product Price'
    )
    ->addColumn(
        'grant_checkout_items',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Checkout Items'
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogpermissions_index_product', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogpermissions_index_product', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_catalogpermissions_index_product',
            ['product_id', 'store_id', 'customer_group_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['product_id', 'store_id', 'customer_group_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]

    )
    ->setComment('Enterprise Catalogpermissions Index Product');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogpermissions_index_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogpermissions_index_tmp'))
    ->addColumn(
        'category_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Category Id'
    )
    ->addColumn(
        'website_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Website Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'grant_catalog_category_view',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Category View'
    )
    ->addColumn(
        'grant_catalog_product_price',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Product Price'
    )
    ->addColumn(
        'grant_checkout_items',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Checkout Items'
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index', ['customer_group_id']),
        ['customer_group_id']
    )
    ->addIndex(
        $this->getIdxName(
            'magento_catalogpermissions_index',
            ['category_id', 'website_id', 'customer_group_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['category_id', 'website_id', 'customer_group_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]

    )
    ->setComment('Catalog Category Permissions Temporary Index');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogpermissions_index_product_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogpermissions_index_product_tmp'))
    ->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Product Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'grant_catalog_category_view',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Category View'
    )
    ->addColumn(
        'grant_catalog_product_price',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Catalog Product Price'
    )
    ->addColumn(
        'grant_checkout_items',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'Grant Checkout Items'
    )
    ->addIndex(
        $this->getIdxName(
            'magento_catalogpermissions_index_product_tmp',
            ['product_id', 'store_id', 'customer_group_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['product_id', 'store_id', 'customer_group_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index_product_tmp', ['store_id']),
        ['store_id']
    )
    ->addIndex(
        $this->getIdxName('magento_catalogpermissions_index_product_tmp', ['customer_group_id']),
        ['customer_group_id']
    )
    ->setComment('Catalog Product Permissions Temporary Index');

$installer->getConnection()->createTable($table);

$installer->endSetup();
