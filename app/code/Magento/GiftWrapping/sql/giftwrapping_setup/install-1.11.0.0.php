<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $this Magento\Setup\Module\SetupModule */
$installer = $this;

/**
 * Create table 'magento_giftwrapping'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftwrapping')
)->addColumn(
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Wrapping Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Status'
)->addColumn(
    'base_price',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false),
    'Base Price'
)->addColumn(
    'image',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Image'
)->addIndex(
    $installer->getIdxName('magento_giftwrapping', array('status')),
    array('status')
)->setComment(
    'Enterprise Gift Wrapping Table'
);
$installer->getConnection()->createTable($table);


/**
 * Create table 'magento_giftwrapping_store_attributes'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftwrapping_store_attributes')
)->addColumn(
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Wrapping Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addColumn(
    'design',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Design'
)->addIndex(
    $installer->getIdxName('magento_giftwrapping_store_attributes', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_giftwrapping_store_attributes',
        'wrapping_id',
        'magento_giftwrapping',
        'wrapping_id'
    ),
    'wrapping_id',
    $installer->getTable('magento_giftwrapping'),
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_giftwrapping_store_attributes', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Gift Wrapping Attribute Table'
);
$installer->getConnection()->createTable($table);


/**
 * Create table 'magento_giftwrapping_website'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftwrapping_website')
)->addColumn(
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Wrapping Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Website Id'
)->addIndex(
    $installer->getIdxName('magento_giftwrapping_website', array('website_id')),
    array('website_id')
)->addForeignKey(
    $installer->getFkName('magento_giftwrapping_website', 'wrapping_id', 'magento_giftwrapping', 'wrapping_id'),
    'wrapping_id',
    $installer->getTable('magento_giftwrapping'),
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_giftwrapping_website', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Gift Wrapping Website Table'
);
$installer->getConnection()->createTable($table);
