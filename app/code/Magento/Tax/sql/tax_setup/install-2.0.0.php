<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer \Magento\Tax\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();
//
/**
 * Create table 'tax_class'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('tax_class')
)->addColumn(
    'class_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Class Id'
)->addColumn(
    'class_name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Class Name'
)->addColumn(
    'class_type',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    8,
    array('nullable' => false, 'default' => 'CUSTOMER'),
    'Class Type'
)->setComment(
    'Tax Class'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'tax_calculation_rule'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('tax_calculation_rule')
)->addColumn(
    'tax_calculation_rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Tax Calculation Rule Id'
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Code'
)->addColumn(
    'priority',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Priority'
)->addColumn(
    'position',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Position'
)->addColumn(
    'calculate_subtotal',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Calculate off subtotal option'
)->addIndex(
    $installer->getIdxName('tax_calculation_rule', array('priority', 'position')),
    array('priority', 'position')
)->addIndex(
    $installer->getIdxName('tax_calculation_rule', array('code')),
    array('code')
)->setComment(
    'Tax Calculation Rule'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'tax_calculation_rate'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('tax_calculation_rate')
)->addColumn(
    'tax_calculation_rate_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Tax Calculation Rate Id'
)->addColumn(
    'tax_country_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    2,
    array('nullable' => false),
    'Tax Country Id'
)->addColumn(
    'tax_region_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Tax Region Id'
)->addColumn(
    'tax_postcode',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    21,
    array(),
    'Tax Postcode'
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Code'
)->addColumn(
    'rate',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false),
    'Rate'
)->addColumn(
    'zip_is_range',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array(),
    'Zip Is Range'
)->addColumn(
    'zip_from',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Zip From'
)->addColumn(
    'zip_to',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true),
    'Zip To'
)->addIndex(
    $installer->getIdxName('tax_calculation_rate', array('tax_country_id', 'tax_region_id', 'tax_postcode')),
    array('tax_country_id', 'tax_region_id', 'tax_postcode')
)->addIndex(
    $installer->getIdxName('tax_calculation_rate', array('code')),
    array('code')
)->addIndex(
    $installer->getIdxName(
        'tax_calculation_rate',
        array('tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode')
    ),
    array('tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode')
)->setComment(
    'Tax Calculation Rate'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'tax_calculation'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('tax_calculation')
)->addColumn(
    'tax_calculation_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Tax Calculation Id'
)->addColumn(
    'tax_calculation_rate_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Tax Calculation Rate Id'
)->addColumn(
    'tax_calculation_rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Tax Calculation Rule Id'
)->addColumn(
    'customer_tax_class_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Customer Tax Class Id'
)->addColumn(
    'product_tax_class_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false),
    'Product Tax Class Id'
)->addIndex(
    $installer->getIdxName('tax_calculation', array('tax_calculation_rule_id')),
    array('tax_calculation_rule_id')
)->addIndex(
    $installer->getIdxName('tax_calculation', array('customer_tax_class_id')),
    array('customer_tax_class_id')
)->addIndex(
    $installer->getIdxName('tax_calculation', array('product_tax_class_id')),
    array('product_tax_class_id')
)->addIndex(
    $installer->getIdxName(
        'tax_calculation',
        array('tax_calculation_rate_id', 'customer_tax_class_id', 'product_tax_class_id')
    ),
    array('tax_calculation_rate_id', 'customer_tax_class_id', 'product_tax_class_id')
)->addForeignKey(
    $installer->getFkName('tax_calculation', 'product_tax_class_id', 'tax_class', 'class_id'),
    'product_tax_class_id',
    $installer->getTable('tax_class'),
    'class_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('tax_calculation', 'customer_tax_class_id', 'tax_class', 'class_id'),
    'customer_tax_class_id',
    $installer->getTable('tax_class'),
    'class_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'tax_calculation',
        'tax_calculation_rate_id',
        'tax_calculation_rate',
        'tax_calculation_rate_id'
    ),
    'tax_calculation_rate_id',
    $installer->getTable('tax_calculation_rate'),
    'tax_calculation_rate_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'tax_calculation',
        'tax_calculation_rule_id',
        'tax_calculation_rule',
        'tax_calculation_rule_id'
    ),
    'tax_calculation_rule_id',
    $installer->getTable('tax_calculation_rule'),
    'tax_calculation_rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Tax Calculation'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'tax_calculation_rate_title'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('tax_calculation_rate_title')
)->addColumn(
    'tax_calculation_rate_title_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Tax Calculation Rate Title Id'
)->addColumn(
    'tax_calculation_rate_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false),
    'Tax Calculation Rate Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Store Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Value'
)->addIndex(
    $installer->getIdxName('tax_calculation_rate_title', array('tax_calculation_rate_id', 'store_id')),
    array('tax_calculation_rate_id', 'store_id')
)->addIndex(
    $installer->getIdxName('tax_calculation_rate_title', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('tax_calculation_rate_title', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'tax_calculation_rate_title',
        'tax_calculation_rate_id',
        'tax_calculation_rate',
        'tax_calculation_rate_id'
    ),
    'tax_calculation_rate_id',
    $installer->getTable('tax_calculation_rate'),
    'tax_calculation_rate_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Tax Calculation Rate Title'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'tax_order_aggregated_created'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('tax_order_aggregated_created')
)->addColumn(
    'id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Id'
)->addColumn(
    'period',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
    null,
    array('nullable' => true),
    'Period'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true),
    'Store Id'
)->addColumn(
    'code',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Code'
)->addColumn(
    'order_status',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    50,
    array('nullable' => false),
    'Order Status'
)->addColumn(
    'percent',
    \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
    null,
    array(),
    'Percent'
)->addColumn(
    'orders_count',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Orders Count'
)->addColumn(
    'tax_base_amount_sum',
    \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
    null,
    array(),
    'Tax Base Amount Sum'
)->addIndex(
    $installer->getIdxName(
        'tax_order_aggregated_created',
        array('period', 'store_id', 'code', 'percent', 'order_status'),
        true
    ),
    array('period', 'store_id', 'code', 'percent', 'order_status'),
    array('type' => 'unique')
)->addIndex(
    $installer->getIdxName('tax_order_aggregated_created', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('tax_order_aggregated_created', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Tax Order Aggregation'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'tax_order_aggregated_updated'
 */
$installer->getConnection()->createTable(
    $installer->getConnection()->createTableByDdl(
        $installer->getTable('tax_order_aggregated_created'),
        $installer->getTable('tax_order_aggregated_updated')
    )
);

/**
 * Create table 'sales_order_tax_item'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('sales_order_tax_item')
)->addColumn(
        'tax_item_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
        'Tax Item Id'
    )->addColumn(
        'tax_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false),
        'Tax Id'
    )->addColumn(
        'item_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => true),
        'Item Id'
    )->addColumn(
        'tax_percent',
        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Real Tax Percent For Item'
    )->addColumn(
        'amount',
        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Tax amount for the item and tax rate'
    )->addColumn(
        'base_amount',
        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Base tax amount for the item and tax rate'
    )->addColumn(
        'real_amount',
        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Real tax amount for the item and tax rate'
    )->addColumn(
        'real_base_amount',
        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        '12,4',
        array('nullable' => false),
        'Real base tax amount for the item and tax rate'
    )->addColumn(
        'associated_item_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('nullable' => true, 'unsigned' => true),
        'Id of the associated item'
    )->addColumn(
        'taxable_item_type',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        32,
        array('nullable' => true),
        'Type of the taxable item'
    )->addIndex(
        $installer->getIdxName('sales_order_tax_item', array('item_id')),
        array('item_id')
    )->addIndex(
        $installer->getIdxName(
            'sales_order_tax_item',
            array('tax_id', 'item_id'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('tax_id', 'item_id'),
        array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )->addForeignKey(
        $installer->getFkName('sales_order_tax_item', 'associated_item_id', 'sales_flat_order_item', 'item_id'),
        'associated_item_id',
        $installer->getTable('sales_flat_order_item'),
        'item_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName('sales_order_tax_item', 'tax_id', 'sales_order_tax', 'tax_id'),
        'tax_id',
        $installer->getTable('sales_order_tax'),
        'tax_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )->addForeignKey(
        $installer->getFkName('sales_order_tax_item', 'item_id', 'sales_flat_order_item', 'item_id'),
        'item_id',
        $installer->getTable('sales_flat_order_item'),
        'item_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )->setComment(
        'Sales Order Tax Item'
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();
