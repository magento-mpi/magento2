<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Module\Setup */

/**
 * Create table 'report_compared_product_index'.
 * In MySQL version this table comes with unique keys to implement insertOnDuplicate(), so that
 * only one record is added when customer/visitor compares same product again.
 */
$table = $this->getConnection()->newTable(
    $this->getTable('report_compared_product_index')
)->addColumn(
    'index_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Index Id'
)->addColumn(
    'visitor_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Visitor Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Customer Id'
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
    array('unsigned' => true),
    'Store Id'
)->addColumn(
    'added_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Added At'
)->addIndex(
    $this->getIdxName(
        'report_compared_product_index',
        array('visitor_id', 'product_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('visitor_id', 'product_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $this->getIdxName(
        'report_compared_product_index',
        array('customer_id', 'product_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('customer_id', 'product_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $this->getIdxName('report_compared_product_index', array('store_id')),
    array('store_id')
)->addIndex(
    $this->getIdxName('report_compared_product_index', array('added_at')),
    array('added_at')
)->addIndex(
    $this->getIdxName('report_compared_product_index', array('product_id')),
    array('product_id')
)->addForeignKey(
    $this->getFkName('report_compared_product_index', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $this->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('report_compared_product_index', 'product_id', 'catalog_product_entity', 'entity_id'),
    'product_id',
    $this->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('report_compared_product_index', 'store_id', 'store', 'store_id'),
    'store_id',
    $this->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Reports Compared Product Index Table'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'report_viewed_product_index'
 * In MySQL version this table comes with unique keys to implement insertOnDuplicate(), so that
 * only one record is added when customer/visitor views same product again.
 */
$table = $this->getConnection()->newTable(
    $this->getTable('report_viewed_product_index')
)->addColumn(
    'index_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Index Id'
)->addColumn(
    'visitor_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Visitor Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Customer Id'
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
    array('unsigned' => true),
    'Store Id'
)->addColumn(
    'added_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Added At'
)->addIndex(
    $this->getIdxName(
        'report_viewed_product_index',
        array('visitor_id', 'product_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('visitor_id', 'product_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $this->getIdxName(
        'report_viewed_product_index',
        array('customer_id', 'product_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('customer_id', 'product_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $this->getIdxName('report_viewed_product_index', array('store_id')),
    array('store_id')
)->addIndex(
    $this->getIdxName('report_viewed_product_index', array('added_at')),
    array('added_at')
)->addIndex(
    $this->getIdxName('report_viewed_product_index', array('product_id')),
    array('product_id')
)->addForeignKey(
    $this->getFkName('report_viewed_product_index', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $this->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('report_viewed_product_index', 'product_id', 'catalog_product_entity', 'entity_id'),
    'product_id',
    $this->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('report_viewed_product_index', 'store_id', 'store', 'store_id'),
    'store_id',
    $this->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Reports Viewed Product Index Table'
);
$this->getConnection()->createTable($table);

$installFile = __DIR__ . '/install-1.6.0.0.php';

/** @var \Magento\Filesystem\Directory\Read $modulesDirectory */
$modulesDirectory = $this->getFilesystem()->getDirectoryRead(\Magento\Framework\App\Filesystem::MODULES_DIR);
if ($modulesDirectory->isExist($modulesDirectory->getRelativePath($installFile))) {
    include $installFile;
}
