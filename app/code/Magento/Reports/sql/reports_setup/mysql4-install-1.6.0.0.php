<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

/**
 * Create table 'report_compared_product_index'.
 * In MySQL version this table comes with unique keys to implement insertOnDuplicate(), so that
 * only one record is added when customer/visitor compares same product again.
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('report_compared_product_index'))
    ->addColumn('index_id', \Magento\DB\Ddl\Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Index Id')
    ->addColumn('visitor_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Visitor Id')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer Id')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('added_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Added At')
    ->addIndex($installer->getIdxName('report_compared_product_index', array('visitor_id', 'product_id'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('visitor_id', 'product_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('report_compared_product_index', array('customer_id', 'product_id'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('customer_id', 'product_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('report_compared_product_index', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('report_compared_product_index', array('added_at')),
        array('added_at'))
    ->addIndex($installer->getIdxName('report_compared_product_index', array('product_id')),
        array('product_id'))
    ->addForeignKey($installer->getFkName('report_compared_product_index', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('report_compared_product_index', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('report_compared_product_index', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Reports Compared Product Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'report_viewed_product_index'
 * In MySQL version this table comes with unique keys to implement insertOnDuplicate(), so that
 * only one record is added when customer/visitor views same product again.
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('report_viewed_product_index'))
    ->addColumn('index_id', \Magento\DB\Ddl\Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Index Id')
    ->addColumn('visitor_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Visitor Id')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer Id')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store Id')
    ->addColumn('added_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Added At')
    ->addIndex($installer->getIdxName('report_viewed_product_index', array('visitor_id', 'product_id'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('visitor_id', 'product_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('report_viewed_product_index', array('customer_id', 'product_id'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('customer_id', 'product_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('report_viewed_product_index', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('report_viewed_product_index', array('added_at')),
        array('added_at'))
    ->addIndex($installer->getIdxName('report_viewed_product_index', array('product_id')),
        array('product_id'))
    ->addForeignKey($installer->getFkName('report_viewed_product_index', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('report_viewed_product_index', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('report_viewed_product_index', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Reports Viewed Product Index Table');
$installer->getConnection()->createTable($table);

$installFile = __DIR__ . DS . 'install-1.6.0.0.php';

$modulesDirectory = $this->filesystem->getDirectoryRead(
    \Magento\Filesystem\DirectoryList::MODULES);
if ($modulesDirectory->isExist($modulesDirectory->getRelativePath($installFile))) {
    include $installFile;
}
