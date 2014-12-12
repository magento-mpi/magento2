<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $this \Magento\Rma\Model\Resource\Setup */

/**
 * Prepare database before module installation
 */
$this->startSetup();

/**
 * Create table 'magento_rma'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'RMA Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Status'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '1'],
    'Is Active'
)->addColumn(
    'increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    [],
    'Increment Id'
)->addColumn(
    'date_requested',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
    'RMA Requested At'
)->addColumn(
    'order_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'Order Id'
)->addColumn(
    'order_increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    [],
    'Order Increment Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Store Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true],
    'Customer Id'
)->addColumn(
    'customer_custom_email',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Customer Custom Email'
)->addColumn(
    'protect_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Protect Code'
)->addIndex(
    $this->getIdxName('magento_rma', ['status']),
    ['status']
)->addIndex(
    $this->getIdxName('magento_rma', ['is_active']),
    ['is_active']
)->addIndex(
    $this->getIdxName('magento_rma', ['increment_id']),
    ['increment_id']
)->addIndex(
    $this->getIdxName('magento_rma', ['date_requested']),
    ['date_requested']
)->addIndex(
    $this->getIdxName('magento_rma', ['order_id']),
    ['order_id']
)->addIndex(
    $this->getIdxName('magento_rma', ['order_increment_id']),
    ['order_increment_id']
)->addIndex(
    $this->getIdxName('magento_rma', ['store_id']),
    ['store_id']
)->addIndex(
    $this->getIdxName('magento_rma', ['customer_id']),
    ['customer_id']
)->addForeignKey(
    $this->getFkName('magento_rma', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $this->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma', 'store_id', 'store', 'store_id'),
    'store_id',
    $this->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA LIst'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_grid'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_grid')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'RMA Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Status'
)->addColumn(
    'increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    [],
    'Increment Id'
)->addColumn(
    'date_requested',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
    'RMA Requested At'
)->addColumn(
    'order_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'Order Id'
)->addColumn(
    'order_increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    [],
    'Order Increment Id'
)->addColumn(
    'order_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    [],
    'Order Created At'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Store Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true],
    'Customer Id'
)->addColumn(
    'customer_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Customer Billing Name'
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['status']),
    ['status']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['increment_id']),
    ['increment_id']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['date_requested']),
    ['date_requested']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['order_id']),
    ['order_id']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['order_increment_id']),
    ['order_increment_id']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['order_date']),
    ['order_date']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['store_id']),
    ['store_id']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['customer_id']),
    ['customer_id']
)->addIndex(
    $this->getIdxName('magento_rma_grid', ['customer_name']),
    ['customer_name']
)->addForeignKey(
    $this->getFkName('magento_rma_grid', 'entity_id', 'magento_rma', 'entity_id'),
    'entity_id',
    $this->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Grid'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_status_history'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_status_history')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Entity Id'
)->addColumn(
    'rma_entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'RMA Entity Id'
)->addColumn(
    'is_customer_notified',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    [],
    'Is Customer Notified'
)->addColumn(
    'is_visible_on_front',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Is Visible On Front'
)->addColumn(
    'comment',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Comment'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Status'
)->addColumn(
    'created_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
    'Created At'
)->addColumn(
    'is_admin',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    [],
    'Is this Merchant Comment'
)->addIndex(
    $this->getIdxName('magento_rma_status_history', ['rma_entity_id']),
    ['rma_entity_id']
)->addIndex(
    $this->getIdxName('magento_rma_status_history', ['created_at']),
    ['created_at']
)->addForeignKey(
    $this->getFkName('magento_rma_status_history', 'rma_entity_id', 'magento_rma', 'entity_id'),
    'rma_entity_id',
    $this->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA status history magento_rma_status_history'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_entity')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Entity Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Type Id'
)->addColumn(
    'attribute_set_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Attribute Set Id'
)->addColumn(
    'rma_entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'RMA entity id'
)->addColumn(
    'is_qty_decimal',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Is Qty Decimal'
)->addColumn(
    'qty_requested',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    ['nullable' => false, 'default' => '0.0000'],
    'Qty of requested for RMA items'
)->addColumn(
    'qty_authorized',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Qty of authorized items'
)->addColumn(
    'qty_approved',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Qty of approved items'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Status'
)->addColumn(
    'order_item_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'Product Order Item Id'
)->addColumn(
    'product_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Product Name'
)->addColumn(
    'qty_returned',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Qty of returned items'
)->addColumn(
    'product_sku',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Product Sku'
)->addColumn(
    'product_admin_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Product Name For Backend'
)->addColumn(
    'product_admin_sku',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Product Sku For Backend'
)->addColumn(
    'product_options',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    null,
    [],
    'Product Options'
)->addIndex(
    $this->getIdxName('magento_rma_item_entity', ['entity_type_id']),
    ['entity_type_id']
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity', 'rma_entity_id', 'magento_rma', 'entity_id'),
    'rma_entity_id',
    $this->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_eav_attribute'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_eav_attribute')
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Attribute Id'
)->addColumn(
    'is_visible',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '1'],
    'Is Visible'
)->addColumn(
    'input_filter',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Input Filter'
)->addColumn(
    'multiline_count',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '1'],
    'Multiline Count'
)->addColumn(
    'validate_rules',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Validate Rules'
)->addColumn(
    'is_system',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Is System'
)->addColumn(
    'sort_order',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Sort Order'
)->addColumn(
    'data_model',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Data Model'
)->addForeignKey(
    $this->getFkName('magento_rma_item_eav_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item EAV Attribute'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_datetime'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_entity_datetime')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'nullable' => false, 'primary' => true],
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
    null,
    ['nullable' => false],
    'Value'
)->addIndex(
    $this->getIdxName(
        'magento_rma_item_entity_datetime',
        ['entity_id', 'attribute_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['entity_id', 'attribute_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_datetime', ['entity_type_id']),
    ['entity_type_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_datetime', ['attribute_id']),
    ['attribute_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_datetime', ['entity_id', 'attribute_id', 'value']),
    ['entity_id', 'attribute_id', 'value']
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_datetime', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_datetime', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $this->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_datetime', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $this->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Datetime'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_decimal'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_entity_decimal')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'nullable' => false, 'primary' => true],
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    ['nullable' => false, 'default' => '0.0000'],
    'Value'
)->addIndex(
    $this->getIdxName(
        'magento_rma_item_entity_decimal',
        ['entity_id', 'attribute_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['entity_id', 'attribute_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_decimal', ['entity_type_id']),
    ['entity_type_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_decimal', ['attribute_id']),
    ['attribute_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_decimal', ['entity_id', 'attribute_id', 'value']),
    ['entity_id', 'attribute_id', 'value']
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_decimal', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_decimal', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $this->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_decimal', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $this->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Decimal'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_int'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_entity_int')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'nullable' => false, 'primary' => true],
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['nullable' => false, 'default' => '0'],
    'Value'
)->addIndex(
    $this->getIdxName(
        'magento_rma_item_entity_int',
        ['entity_id', 'attribute_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['entity_id', 'attribute_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_int', ['entity_type_id']),
    ['entity_type_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_int', ['attribute_id']),
    ['attribute_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_int', ['entity_id', 'attribute_id', 'value']),
    ['entity_id', 'attribute_id', 'value']
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_int', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_int', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $this->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_int', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $this->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Int'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_text'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_entity_text')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'nullable' => false, 'primary' => true],
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    ['nullable' => false],
    'Value'
)->addIndex(
    $this->getIdxName(
        'magento_rma_item_entity_text',
        ['entity_id', 'attribute_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['entity_id', 'attribute_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_text', ['entity_type_id']),
    ['entity_type_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_text', ['attribute_id']),
    ['attribute_id']
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_text', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_text', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $this->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_text', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $this->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Text'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_varchar'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_entity_varchar')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'nullable' => false, 'primary' => true],
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Value'
)->addIndex(
    $this->getIdxName(
        'magento_rma_item_entity_varchar',
        ['entity_id', 'attribute_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['entity_id', 'attribute_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_varchar', ['entity_type_id']),
    ['entity_type_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_varchar', ['attribute_id']),
    ['attribute_id']
)->addIndex(
    $this->getIdxName('magento_rma_item_entity_varchar', ['entity_id', 'attribute_id', 'value']),
    ['entity_id', 'attribute_id', 'value']
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_varchar', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_varchar', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $this->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_entity_varchar', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $this->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Varchar'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_form_attribute'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_form_attribute')
)->addColumn(
    'form_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    ['nullable' => false, 'primary' => true],
    'Form Code'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Attribute Id'
)->addIndex(
    $this->getIdxName('magento_rma_item_form_attribute', ['attribute_id']),
    ['attribute_id']
)->addForeignKey(
    $this->getFkName('magento_rma_item_form_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Form Attribute'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_eav_attribute_website'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_item_eav_attribute_website')
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Attribute Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true],
    'Website Id'
)->addColumn(
    'is_visible',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Is Visible'
)->addColumn(
    'is_required',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Is Required'
)->addColumn(
    'default_value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Default Value'
)->addColumn(
    'multiline_count',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Multiline Count'
)->addIndex(
    $this->getIdxName('magento_rma_item_eav_attribute_website', ['website_id']),
    ['website_id']
)->addForeignKey(
    $this->getFkName('magento_rma_item_eav_attribute_website', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $this->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('magento_rma_item_eav_attribute_website', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $this->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise RMA Item Eav Attribute Website'
);
$this->getConnection()->createTable($table);

//TODO: should be refactored in order to avoid sales table modification
$this->getConnection()->addColumn(
    $this->getTable('sales_order_item'),
    'qty_returned',
    [
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'SCALE' => 4,
        'PRECISION' => 12,
        'DEFAULT' => '0.0000',
        'NULLABLE' => false,
        'COMMENT' => 'Qty of returned items'
    ]
);

/**
 * Create table 'magento_rma_shipping_label'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('magento_rma_shipping_label')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Entity Id'
)->addColumn(
    'rma_entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false],
    'RMA Entity Id'
)->addColumn(
    'shipping_label',
    \Magento\Framework\DB\Ddl\Table::TYPE_VARBINARY,
    '2M',
    [],
    'Shipping Label Content'
)->addColumn(
    'packages',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    20000,
    [],
    'Packed Products in Packages'
)->addColumn(
    'track_number',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Tracking Number'
)->addColumn(
    'carrier_title',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Carrier Title'
)->addColumn(
    'method_title',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    [],
    'Method Title'
)->addColumn(
    'carrier_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Carrier Code'
)->addColumn(
    'method_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Method Code'
)->addColumn(
    'price',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Price'
)->addColumn(
    'is_admin',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    6,
    [],
    'Is this Label Created by Merchant'
)->addForeignKey(
    $this->getFkName('magento_rma_shipping_label', 'rma_entity_id', 'magento_rma', 'entity_id'),
    'rma_entity_id',
    $this->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'List of RMA Shipping Labels'
);
$this->getConnection()->createTable($table);

$this->endSetup();
