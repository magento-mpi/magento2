<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;


$installer->startSetup();

/**
 * Create table 'magento_catalogpermissions'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_catalogpermissions')
)->addColumn(
    'permission_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Permission Id'
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
    array('unsigned' => true),
    'Website Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Customer Group Id'
)->addColumn(
    'grant_catalog_category_view',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Grant Catalog Category View'
)->addColumn(
    'grant_catalog_product_price',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Grant Catalog Product Price'
)->addColumn(
    'grant_checkout_items',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Grant Checkout Items'
)->addIndex(
    $installer->getIdxName(
        'magento_catalogpermissions',
        array('category_id', 'website_id', 'customer_group_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('category_id', 'website_id', 'customer_group_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_catalogpermissions', array('website_id')),
    array('website_id')
)->addIndex(
    $installer->getIdxName('magento_catalogpermissions', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName('magento_catalogpermissions', 'category_id', 'catalog_category_entity', 'entity_id'),
    'category_id',
    $installer->getTable('catalog_category_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_catalogpermissions', 'customer_group_id', 'customer_group', 'customer_group_id'),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_catalogpermissions', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Catalogpermissions'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogpermissions_index'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_catalogpermissions_index')
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
    $installer->getIdxName('magento_catalogpermissions_index', array('website_id')),
    array('website_id')
)->addIndex(
    $installer->getIdxName('magento_catalogpermissions_index', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_catalogpermissions_index',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    ),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_catalogpermissions_index', 'category_id', 'catalog_category_entity', 'entity_id'),
    'category_id',
    $installer->getTable('catalog_category_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_catalogpermissions_index', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Catalogpermissions Index'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogpermissions_index_product'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_catalogpermissions_index_product')
)->addColumn(
    'product_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Product Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Store Id'
)->addColumn(
    'category_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Category Id'
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
)->addColumn(
    'is_config',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'default' => '0'),
    'Is Config'
)->addIndex(
    $installer->getIdxName(
        'magento_catalogpermissions_index_product',
        array('product_id', 'store_id', 'category_id', 'customer_group_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'store_id', 'category_id', 'customer_group_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_catalogpermissions_index_product', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_catalogpermissions_index_product', array('customer_group_id')),
    array('customer_group_id')
)->addIndex(
    $installer->getIdxName('magento_catalogpermissions_index_product', array('category_id')),
    array('category_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_catalogpermissions_index_product',
        'product_id',
        'catalog_product_entity',
        'entity_id'
    ),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'magento_catalogpermissions_index_product',
        'category_id',
        'catalog_category_entity',
        'entity_id'
    ),
    'category_id',
    $installer->getTable('catalog_category_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'magento_catalogpermissions_index_product',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    ),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_catalogpermissions_index_product', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Catalogpermissions Index Product'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
