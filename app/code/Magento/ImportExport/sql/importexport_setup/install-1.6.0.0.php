<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\ImportExport\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'importexport_importdata'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('importexport_importdata'))
    ->addColumn('id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Id')
    ->addColumn('entity', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        ), 'Entity')
    ->addColumn('behavior', \Magento\DB\Ddl\Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        'default'   => \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND,
        ), 'Behavior')
    ->addColumn('data', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        'default'   => '',
        ), 'Data')
    ->setComment('Import Data Table');
$installer->getConnection()->createTable($table);

/**
 * Add unique key for parent-child pairs which makes easier configurable products import
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_super_link'),
    $installer->getIdxName(
        'catalog_product_super_link',
        array('product_id', 'parent_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'parent_id'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog_product_super_attribute' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_super_attribute'),
    $installer->getIdxName(
        'catalog_product_super_attribute',
        array('product_id', 'attribute_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'attribute_id'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog_product_super_attribute_pricing' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_super_attribute_pricing'),
    $installer->getIdxName(
        'catalog_product_super_attribute_pricing',
        array('product_super_attribute_id', 'value_index', 'website_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_super_attribute_id', 'value_index', 'website_id'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

/**
 * Add unique key for 'catalog_product_link_attribute_int' table
 */
$installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_link_attribute_int'),
    $installer->getIdxName(
        'catalog_product_link_attribute_int',
        array('product_link_attribute_id', 'link_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_link_attribute_id', 'link_id'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

/**
 * Add foreign keys for 'catalog_product_link_attribute_int' table
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog_product_link_attribute_int',
        'link_id',
        'catalog_product_link',
        'link_id'
    ),
    $installer->getTable('catalog_product_link_attribute_int'),
    'link_id',
    $installer->getTable('catalog_product_link'),
    'link_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'catalog_product_link_attribute_int',
        'product_link_attribute_id',
        'catalog_product_link_attribute',
        'product_link_attribute_id'
    ),
    $installer->getTable('catalog_product_link_attribute_int'),
    'product_link_attribute_id',
    $installer->getTable('catalog_product_link_attribute'),
    'product_link_attribute_id'
);

$installer->endSetup();
