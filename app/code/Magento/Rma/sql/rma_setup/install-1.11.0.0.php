<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Setup\Module\SetupModule */
$installer = $this;

/**
 * Prepare database before module installation
 */
$installer->startSetup();

/**
 * Create table 'magento_rma'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'RMA Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array(),
    'Status'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '1'),
    'Is Active'
)->addColumn(
    'increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Increment Id'
)->addColumn(
    'date_requested',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT),
    'RMA Requested At'
)->addColumn(
    'order_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Order Id'
)->addColumn(
    'order_increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Order Increment Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Store Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Customer Id'
)->addColumn(
    'customer_custom_email',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Customer Custom Email'
)->addIndex(
    $installer->getIdxName('magento_rma', array('status')),
    array('status')
)->addIndex(
    $installer->getIdxName('magento_rma', array('is_active')),
    array('is_active')
)->addIndex(
    $installer->getIdxName('magento_rma', array('increment_id')),
    array('increment_id')
)->addIndex(
    $installer->getIdxName('magento_rma', array('date_requested')),
    array('date_requested')
)->addIndex(
    $installer->getIdxName('magento_rma', array('order_id')),
    array('order_id')
)->addIndex(
    $installer->getIdxName('magento_rma', array('order_increment_id')),
    array('order_increment_id')
)->addIndex(
    $installer->getIdxName('magento_rma', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_rma', array('customer_id')),
    array('customer_id')
)->addForeignKey(
    $installer->getFkName('magento_rma', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $installer->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA LIst'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_grid'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_grid')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'RMA Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array(),
    'Status'
)->addColumn(
    'increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Increment Id'
)->addColumn(
    'date_requested',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT),
    'RMA Requested At'
)->addColumn(
    'order_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Order Id'
)->addColumn(
    'order_increment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array(),
    'Order Increment Id'
)->addColumn(
    'order_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array(),
    'Order Created At'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Store Id'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Customer Id'
)->addColumn(
    'customer_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Customer Billing Name'
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('status')),
    array('status')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('increment_id')),
    array('increment_id')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('date_requested')),
    array('date_requested')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('order_id')),
    array('order_id')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('order_increment_id')),
    array('order_increment_id')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('order_date')),
    array('order_date')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('customer_id')),
    array('customer_id')
)->addIndex(
    $installer->getIdxName('magento_rma_grid', array('customer_name')),
    array('customer_name')
)->addForeignKey(
    $installer->getFkName('magento_rma_grid', 'entity_id', 'magento_rma', 'entity_id'),
    'entity_id',
    $installer->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Grid'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_status_history'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_status_history')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity Id'
)->addColumn(
    'rma_entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'RMA Entity Id'
)->addColumn(
    'is_customer_notified',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Is Customer Notified'
)->addColumn(
    'is_visible_on_front',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Is Visible On Front'
)->addColumn(
    'comment',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Comment'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array(),
    'Status'
)->addColumn(
    'created_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    array('default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT),
    'Created At'
)->addColumn(
    'is_admin',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Is this Merchant Comment'
)->addIndex(
    $installer->getIdxName('magento_rma_status_history', array('rma_entity_id')),
    array('rma_entity_id')
)->addIndex(
    $installer->getIdxName('magento_rma_status_history', array('created_at')),
    array('created_at')
)->addForeignKey(
    $installer->getFkName('magento_rma_status_history', 'rma_entity_id', 'magento_rma', 'entity_id'),
    'rma_entity_id',
    $installer->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA status history magento_rma_status_history'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_entity')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Type Id'
)->addColumn(
    'attribute_set_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute Set Id'
)->addColumn(
    'rma_entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'RMA entity id'
)->addColumn(
    'is_qty_decimal',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Is Qty Decimal'
)->addColumn(
    'qty_requested',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Qty of requested for RMA items'
)->addColumn(
    'qty_authorized',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array(),
    'Qty of authorized items'
)->addColumn(
    'qty_approved',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array(),
    'Qty of approved items'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array(),
    'Status'
)->addColumn(
    'order_item_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Product Order Item Id'
)->addColumn(
    'product_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Product Name'
)->addColumn(
    'product_sku',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Product Sku'
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity', array('entity_type_id')),
    array('entity_type_id')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity', 'rma_entity_id', 'magento_rma', 'entity_id'),
    'rma_entity_id',
    $installer->getTable('magento_rma'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_eav_attribute'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_eav_attribute')
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Attribute Id'
)->addColumn(
    'is_visible',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '1'),
    'Is Visible'
)->addColumn(
    'input_filter',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Input Filter'
)->addColumn(
    'multiline_count',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '1'),
    'Multiline Count'
)->addColumn(
    'validate_rules',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Validate Rules'
)->addColumn(
    'is_system',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Is System'
)->addColumn(
    'sort_order',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Sort Order'
)->addColumn(
    'data_model',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Data Model'
)->addForeignKey(
    $installer->getFkName('magento_rma_item_eav_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item EAV Attribute'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'customer_entity_datetime'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_entity_datetime')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
    null,
    array('nullable' => false),
    'Value'
)->addIndex(
    $installer->getIdxName(
        'magento_rma_item_entity_datetime',
        array('entity_id', 'attribute_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_datetime', array('entity_type_id')),
    array('entity_type_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_datetime', array('attribute_id')),
    array('attribute_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_datetime', array('entity_id', 'attribute_id', 'value')),
    array('entity_id', 'attribute_id', 'value')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_datetime', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_datetime', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_datetime', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Datetime'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_decimal'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_entity_decimal')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Value'
)->addIndex(
    $installer->getIdxName(
        'magento_rma_item_entity_decimal',
        array('entity_id', 'attribute_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_decimal', array('entity_type_id')),
    array('entity_type_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_decimal', array('attribute_id')),
    array('attribute_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_decimal', array('entity_id', 'attribute_id', 'value')),
    array('entity_id', 'attribute_id', 'value')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_decimal', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_decimal', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_decimal', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Decimal'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_int'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_entity_int')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'default' => '0'),
    'Value'
)->addIndex(
    $installer->getIdxName(
        'magento_rma_item_entity_int',
        array('entity_id', 'attribute_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_int', array('entity_type_id')),
    array('entity_type_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_int', array('attribute_id')),
    array('attribute_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_int', array('entity_id', 'attribute_id', 'value')),
    array('entity_id', 'attribute_id', 'value')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_int', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_int', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_int', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Int'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_text'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_entity_text')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array('nullable' => false),
    'Value'
)->addIndex(
    $installer->getIdxName(
        'magento_rma_item_entity_text',
        array('entity_id', 'attribute_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_text', array('entity_type_id')),
    array('entity_type_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_text', array('attribute_id')),
    array('attribute_id')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_text', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_text', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_text', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Text'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_entity_varchar'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_entity_varchar')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Value Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Type Id'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Value'
)->addIndex(
    $installer->getIdxName(
        'magento_rma_item_entity_varchar',
        array('entity_id', 'attribute_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id'),
    array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_varchar', array('entity_type_id')),
    array('entity_type_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_varchar', array('attribute_id')),
    array('attribute_id')
)->addIndex(
    $installer->getIdxName('magento_rma_item_entity_varchar', array('entity_id', 'attribute_id', 'value')),
    array('entity_id', 'attribute_id', 'value')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_varchar', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_varchar', 'entity_id', 'magento_rma_item_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('magento_rma_item_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_entity_varchar', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Entity Varchar'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_form_attribute'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_form_attribute')
)->addColumn(
    'form_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    array('nullable' => false, 'primary' => true),
    'Form Code'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Attribute Id'
)->addIndex(
    $installer->getIdxName('magento_rma_item_form_attribute', array('attribute_id')),
    array('attribute_id')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_form_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'RMA Item Form Attribute'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_rma_item_eav_attribute_website'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_rma_item_eav_attribute_website')
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Attribute Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Website Id'
)->addColumn(
    'is_visible',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Is Visible'
)->addColumn(
    'is_required',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Is Required'
)->addColumn(
    'default_value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    array(),
    'Default Value'
)->addColumn(
    'multiline_count',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Multiline Count'
)->addIndex(
    $installer->getIdxName('magento_rma_item_eav_attribute_website', array('website_id')),
    array('website_id')
)->addForeignKey(
    $installer->getFkName('magento_rma_item_eav_attribute_website', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_rma_item_eav_attribute_website', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise RMA Item Eav Attribute Website'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
