<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Eav\Model\Entity\Setup */

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
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Segment Id'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Name'
)->addColumn(
    'description',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Description'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Is Active'
)->addColumn(
    'conditions_serialized',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '2M',
    array(),
    'Conditions Serialized'
)->addColumn(
    'processing_frequency',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Processing Frequency'
)->addColumn(
    'condition_sql',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '2M',
    array(),
    'Condition Sql'
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
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Segment Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Website Id'
)->addIndex(
    $installer->getIdxName('magento_customersegment_website', array('website_id')),
    array('website_id')
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
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Segment Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Customer Id'
)->addColumn(
    'added_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Added Date'
)->addColumn(
    'updated_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('nullable' => false),
    'Updated Date'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Website Id'
)->addIndex(
    $installer->getIdxName(
        'magento_customersegment_customer',
        array('segment_id', 'website_id', 'customer_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('segment_id', 'website_id', 'customer_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_customersegment_customer', array('website_id')),
    array('website_id')
)->addIndex(
    $installer->getIdxName('magento_customersegment_customer', array('customer_id')),
    array('customer_id')
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
    array('unsigned' => true, 'nullable' => false),
    'Segment Id'
)->addColumn(
    'event',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Event'
)->addIndex(
    $installer->getIdxName('magento_customersegment_event', array('event')),
    array('event')
)->addIndex(
    $installer->getIdxName('magento_customersegment_event', array('segment_id')),
    array('segment_id')
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
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Customer Segment'
    )
);

$installer->endSetup();
