<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $this \Magento\Core\Model\Resource\Setup */
$this->startSetup();

/** @var \Magento\DB\Adapter\AdapterInterface $connection */
$connection = $this->getConnection();

$connection->addIndex(
    $this->getTable('magento_catalogpermissions_index'),
    $this->getIdxName(
        'magento_catalogpermissions_index',
        ['category_id', 'website_id', 'customer_group_id'],
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['category_id', 'website_id', 'customer_group_id'],
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$connection->addIndex(
    $this->getTable('magento_catalogpermissions_index_tmp'),
    $this->getIdxName(
        'magento_catalogpermissions_index',
        ['category_id', 'website_id', 'customer_group_id'],
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['category_id', 'website_id', 'customer_group_id'],
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$connection->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getFkName(
        'magento_catalogpermissions_index_product', 'product_id', 'catalog_product_entity', 'entity_id'
    )
);
$connection->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getFkName(
        'magento_catalogpermissions_index_product', 'category_id', 'catalog_category_entity', 'entity_id'
    )
);
$connection->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getFkName(
        'magento_catalogpermissions_index_product', 'customer_group_id', 'customer_group', 'customer_group_id'
    )
);
$connection->dropForeignKey(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getFkName('magento_catalogpermissions_index_product', 'store_id', 'store', 'store_id')
);
$connection->dropIndex(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getIdxName('magento_catalogpermissions_index_product', ['category_id'])
);
$connection->dropIndex(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getIdxName(
        'magento_catalogpermissions_index_product', ['product_id', 'store_id', 'category_id', 'customer_group_id']
    )
);
$connection->dropColumn($this->getTable('magento_catalogpermissions_index_product'), 'category_id');
$connection->dropColumn($this->getTable('magento_catalogpermissions_index_product'), 'is_config');
$connection->addIndex(
    $this->getTable('magento_catalogpermissions_index_product'),
    $this->getIdxName(
        'magento_catalogpermissions_index_product',
        ['product_id', 'store_id', 'customer_group_id'],
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['product_id', 'store_id', 'customer_group_id'],
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$table = $connection->newTable($this->getTable('magento_catalogpermissions_index_product_tmp'))
    ->addColumn(
        'product_id',
        \Magento\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned'  => true, 'nullable'  => false],
        'Product Id'
    )
    ->addColumn(
        'store_id',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned'  => true, 'nullable'  => false],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned'  => true, 'nullable'  => false],
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
        $this->getIdxName(
            'magento_catalogpermissions_index_product_tmp',
            ['product_id', 'store_id', 'customer_group_id'],
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['product_id', 'store_id', 'customer_group_id'],
        ['type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
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

$connection->createTable($table);

$this->endSetup();
