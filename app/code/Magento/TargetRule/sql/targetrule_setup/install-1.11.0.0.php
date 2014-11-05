<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();
/**
 * Create table 'magento_targetrule'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule')
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addColumn(
    'name',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Name'
)->addColumn(
    'from_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
    null,
    array(),
    'From Date'
)->addColumn(
    'to_date',
    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
    null,
    array(),
    'To Date'
)->addColumn(
    'is_active',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('nullable' => false, 'default' => '0'),
    'Is Active'
)->addColumn(
    'conditions_serialized',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64K',
    array('nullable' => false),
    'Conditions Serialized'
)->addColumn(
    'actions_serialized',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64K',
    array('nullable' => false),
    'Actions Serialized'
)->addColumn(
    'positions_limit',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('nullable' => false, 'default' => '0'),
    'Positions Limit'
)->addColumn(
    'apply_to',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Apply To'
)->addColumn(
    'sort_order',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array(),
    'Sort Order'
)->addColumn(
    'use_customer_segment',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Use Customer Segment'
)->addColumn(
    'action_select',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64K',
    array(),
    'Action Select'
)->addColumn(
    'action_select_bind',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    '64K',
    array(),
    'Action Select Bind'
)->addIndex(
    $installer->getIdxName('magento_targetrule', array('is_active')),
    array('is_active')
)->addIndex(
    $installer->getIdxName('magento_targetrule', array('apply_to')),
    array('apply_to')
)->addIndex(
    $installer->getIdxName('magento_targetrule', array('sort_order')),
    array('sort_order')
)->addIndex(
    $installer->getIdxName('magento_targetrule', array('use_customer_segment')),
    array('use_customer_segment')
)->addIndex(
    $installer->getIdxName('magento_targetrule', array('from_date', 'to_date')),
    array('from_date', 'to_date')
)->setComment(
    'Enterprise Targetrule'
);
$installer->getConnection()->createTable($table);


/**
 * Create table 'magento_targetrule_customersegment'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule_customersegment')
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addColumn(
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Segment Id'
)->addIndex(
    $installer->getIdxName('magento_targetrule_customersegment', array('segment_id')),
    array('segment_id')
)->addForeignKey(
    $installer->getFkName('magento_targetrule_customersegment', 'rule_id', 'magento_targetrule', 'rule_id'),
    'rule_id',
    $installer->getTable('magento_targetrule'),
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'magento_targetrule_customersegment',
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
    'Enterprise Targetrule Customersegment'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_product'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule_product')
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addColumn(
    'product_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Product Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addIndex(
    $installer->getIdxName('magento_targetrule_product', array('product_id')),
    array('product_id')
)->addIndex(
    $installer->getIdxName('magento_targetrule_product', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName('magento_targetrule_product', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_product', 'product_id', 'catalog_product_entity', 'entity_id'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_product', 'rule_id', 'magento_targetrule', 'rule_id'),
    'rule_id',
    $installer->getTable('magento_targetrule'),
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Targetrule Product'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule_index')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Customer Group Id'
)->addColumn(
    'type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Type Id'
)->addColumn(
    'flag',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '1'),
    'Flag'
)->addIndex(
    $installer->getIdxName('magento_targetrule_index', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_targetrule_index', array('customer_group_id')),
    array('customer_group_id')
)->addIndex(
    $installer->getIdxName('magento_targetrule_index', array('type_id')),
    array('type_id')
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index', 'customer_group_id', 'customer_group', 'customer_group_id'),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index', 'entity_id', 'catalog_product_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Targetrule Index'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_related'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule_index_related')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Customer Group Id'
)->addColumn(
    'product_ids',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Related Product Ids'
)->addIndex(
    $installer->getIdxName('magento_targetrule_index_related', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_targetrule_index_related', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_targetrule_index_related',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    ),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index_related', 'entity_id', 'catalog_product_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index_related', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Targetrule Index Related'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_upsell'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule_index_upsell')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Customer Group Id'
)->addColumn(
    'product_ids',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Upsell Product Ids'
)->addIndex(
    $installer->getIdxName('magento_targetrule_index_upsell', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_targetrule_index_upsell', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_targetrule_index_upsell',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    ),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index_upsell', 'entity_id', 'catalog_product_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index_upsell', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Targetrule Index Upsell'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_crosssell'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_targetrule_index_crosssell')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Entity Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Customer Group Id'
)->addColumn(
    'product_ids',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'CrossSell Product Ids'
)->addIndex(
    $installer->getIdxName('magento_targetrule_index_crosssell', array('store_id')),
    array('store_id')
)->addIndex(
    $installer->getIdxName('magento_targetrule_index_crosssell', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_targetrule_index_crosssell',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    ),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index_crosssell', 'entity_id', 'catalog_product_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_targetrule_index_crosssell', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Targetrule Index Crosssell'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
