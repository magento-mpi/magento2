<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Setup\Module\SetupModule */
$this->startSetup();

/**
 * Create table 'recurring_payment'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('recurring_payment')
)->addColumn(
    'payment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Payment Id'
)->addColumn(
    'state',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    20,
    ['nullable' => false],
    'State'
)->addColumn(
    'customer_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true],
    'Customer Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Store Id'
)->addColumn(
    'method_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    ['nullable' => false],
    'Method Code'
)->addColumn(
    'created_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
    'Created At'
)->addColumn(
    'updated_at',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
    'Updated At'
)->addColumn(
    'reference_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    32,
    [],
    'Reference Id'
)->addColumn(
    'subscriber_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    150,
    [],
    'Subscriber Name'
)->addColumn(
    'start_datetime',
    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
    null,
    ['nullable' => false],
    'Start Datetime'
)->addColumn(
    'internal_reference_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    42,
    ['nullable' => false],
    'Internal Reference Id'
)->addColumn(
    'schedule_description',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    ['nullable' => false],
    'Schedule Description'
)->addColumn(
    'suspension_threshold',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Suspension Threshold'
)->addColumn(
    'bill_failed_later',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Bill Failed Later'
)->addColumn(
    'period_unit',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    20,
    ['nullable' => false],
    'Period Unit'
)->addColumn(
    'period_frequency',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Period Frequency'
)->addColumn(
    'period_max_cycles',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Period Max Cycles'
)->addColumn(
    'billing_amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    ['nullable' => false, 'default' => '0.0000'],
    'Billing Amount'
)->addColumn(
    'trial_period_unit',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    20,
    [],
    'Trial Period Unit'
)->addColumn(
    'trial_period_frequency',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Trial Period Frequency'
)->addColumn(
    'trial_period_max_cycles',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true],
    'Trial Period Max Cycles'
)->addColumn(
    'trial_billing_amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    null,
    [],
    'Trial Billing Amount'
)->addColumn(
    'currency_code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    3,
    ['nullable' => false],
    'Currency Code'
)->addColumn(
    'shipping_amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Shipping Amount'
)->addColumn(
    'tax_amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Tax Amount'
)->addColumn(
    'init_amount',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    [],
    'Init Amount'
)->addColumn(
    'init_may_fail',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Init May Fail'
)->addColumn(
    'order_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    ['nullable' => false],
    'Order Info'
)->addColumn(
    'order_item_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    ['nullable' => false],
    'Order Item Info'
)->addColumn(
    'billing_address_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    ['nullable' => false],
    'Billing Address Info'
)->addColumn(
    'shipping_address_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Shipping Address Info'
)->addColumn(
    'payment_vendor_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Payment Vendor Info'
)->addColumn(
    'additional_info',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64k',
    [],
    'Additional Info'
)->addIndex(
    $this->getIdxName(
        'recurring_payment',
        ['internal_reference_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['internal_reference_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('recurring_payment', ['customer_id']),
    ['customer_id']
)->addIndex(
    $this->getIdxName('recurring_payment', ['store_id']),
    ['store_id']
)->addForeignKey(
    $this->getFkName('recurring_payment', 'customer_id', 'customer_entity', 'entity_id'),
    'customer_id',
    $this->getTable('customer_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('recurring_payment', 'store_id', 'store', 'store_id'),
    'store_id',
    $this->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Sales Recurring Payment'
);
$this->getConnection()->createTable($table);

/**
 * Create table 'recurring_payment_order'
 */
$table = $this->getConnection()->newTable(
    $this->getTable('recurring_payment_order')
)->addColumn(
    'link_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
    'Link Id'
)->addColumn(
    'payment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Payment Id'
)->addColumn(
    'order_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
    'Order Id'
)->addIndex(
    $this->getIdxName(
        'recurring_payment_order',
        ['payment_id', 'order_id'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['payment_id', 'order_id'],
    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
)->addIndex(
    $this->getIdxName('recurring_payment_order', ['order_id']),
    ['order_id']
)->addForeignKey(
    $this->getFkName('recurring_payment_order', 'order_id', 'sales_order', 'entity_id'),
    'order_id',
    $this->getTable('sales_order'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $this->getFkName('recurring_payment_order', 'payment_id', 'recurring_payment', 'payment_id'),
    'payment_id',
    $this->getTable('recurring_payment'),
    'payment_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Sales Recurring Payment Order'
);
$this->getConnection()->createTable($table);

$this->endSetup();
