<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'magento_customersegment_segment'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_customersegment_segment')
)->addColumn(
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Segment Id'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Name'
)->addColumn(
    'description',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Description'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['nullable' => false, 'default' => '0'],
    'Is Active'
)->addColumn(
    'conditions_serialized',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '2M',
    [],
    'Conditions Serialized'
)->addColumn(
    'processing_frequency',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['nullable' => false],
    'Processing Frequency'
)->addColumn(
    'condition_sql',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '2M',
    [],
    'Condition Sql'
)->addColumn(
    'apply_to',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Customer types to which this segment applies'
)->setComment(
    'Enterprise Customersegment Segment'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customersegment_website'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_customersegment_website')
)->addColumn(
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Segment Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Website Id'
)->addIndex(
    $installer->getIdxName('magento_customersegment_website', ['website_id']),
    ['website_id']
)->addForeignKey(
    $installer->getFkName(
        'magento_customersegment_website',
        'segment_id',
        'magento_customersegment_segment',
        'segment_id'
    ),
    'segment_id',
    $installer->getTable('magento_customersegment_segment'),
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_customersegment_website', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Customersegment Website'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customersegment_customer'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_customersegment_customer')
)->addColumn(
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Segment Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Customer Id'
)->addColumn(
    'added_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false],
    'Added Date'
)->addColumn(
    'updated_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false],
    'Updated Date'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Website Id'
)->addIndex(
    $installer->getIdxName(
        'magento_customersegment_customer',
        ['segment_id', 'website_id', 'customer_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['segment_id', 'website_id', 'customer_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $installer->getIdxName('magento_customersegment_customer', ['website_id']),
    ['website_id']
)->addIndex(
    $installer->getIdxName('magento_customersegment_customer', ['customer_id']),
    ['customer_id']
)->addForeignKey(
    $installer->getFkName('magento_customersegment_customer', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_customersegment_customer', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'magento_customersegment_customer',
        'segment_id',
        'magento_customersegment_segment',
        'segment_id'
    ),
    'segment_id',
    $installer->getTable('magento_customersegment_segment'),
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Customersegment Customer'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customersegment_event'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_customersegment_event')
)->addColumn(
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'Segment Id'
)->addColumn(
    'event',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Event'
)->addIndex(
    $installer->getIdxName('magento_customersegment_event', ['event']),
    ['event']
)->addIndex(
    $installer->getIdxName('magento_customersegment_event', ['segment_id']),
    ['segment_id']
)->addForeignKey(
    $installer->getFkName(
        'magento_customersegment_event',
        'segment_id',
        'magento_customersegment_segment',
        'segment_id'
    ),
    'segment_id',
    $installer->getTable('magento_customersegment_segment'),
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Customersegment Event'
);
$installer->getConnection()->createTable($table);

// add field that indicates that attribute is used for customer segments to attribute properties
$installer->getConnection()->addColumn(
    $installer->getTable('customer_eav_attribute'),
    'is_used_for_customer_segment',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Customer Segment'
    ]
);

$installer->endSetup();
