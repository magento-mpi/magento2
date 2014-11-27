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
 * Create table 'magento_targetrule'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'name',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'Name'
    )
    ->addColumn(
        'from_date',
        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
        null,
        [],
        'From Date'
    )
    ->addColumn(
        'to_date',
        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
        null,
        [],
        'To Date'
    )
    ->addColumn(
        'is_active',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false, 'default' => '0'],
        'Is Active'
    )
    ->addColumn(
        'conditions_serialized',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64K',
        ['nullable' => false],
        'Conditions Serialized'
    )
    ->addColumn(
        'actions_serialized',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64K',
        ['nullable' => false],
        'Actions Serialized'
    )
    ->addColumn(
        'positions_limit',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['nullable' => false, 'default' => '0'],
        'Positions Limit'
    )
    ->addColumn(
        'apply_to',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Apply To'
    )
    ->addColumn(
        'sort_order',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [],
        'Sort Order'
    )
    ->addColumn(
        'use_customer_segment',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Deprecated after 1.11.2.0'
    )
    ->addColumn(
        'action_select',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64K',
        [],
        'Action Select'
    )
    ->addColumn(
        'action_select_bind',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64K',
        [],
        'Action Select Bind'
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule', ['is_active']),
        ['is_active']
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule', ['apply_to']),
        ['apply_to']
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule', ['sort_order']),
        ['sort_order']
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule', ['use_customer_segment']),
        ['use_customer_segment']
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule', ['from_date', 'to_date']),
        ['from_date', 'to_date']
    )
    ->setComment('Enterprise Targetrule');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_customersegment'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_customersegment'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'segment_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Segment Id'
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule_customersegment', ['segment_id']),
        ['segment_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_targetrule_customersegment', 'rule_id', 'magento_targetrule', 'rule_id'),
        'rule_id',
        $installer->getTable('magento_targetrule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
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
    )
    ->setComment('Enterprise Targetrule Customersegment');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_product'))
    ->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule Id'
    )
    ->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Product Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Deprecated after 1.11.2.0'
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule_product', ['product_id']),
        ['product_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_targetrule_product', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_targetrule_product', 'store_id', 'store', 'store_id'),
        'store_id',
        $installer->getTable('store'),
        'store_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_targetrule_product', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id',
        $installer->getTable('catalog_product_entity'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_targetrule_product', 'rule_id', 'magento_targetrule', 'rule_id'),
        'rule_id',
        $installer->getTable('magento_targetrule'),
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Targetrule Product');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index'))
    ->addColumn(
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Entity Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Customer Group Id'
    )
    ->addColumn(
        'type_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Type Id'
    )
    ->addColumn(
        'flag',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '1'],
        'Flag'
    )
    ->addColumn(
        'customer_segment_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['nullable' => false, 'default' => '0', 'primary' => true],
        'Customer Segment Id'
    )
    ->setComment('Enterprise Targetrule Index');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_related'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index_related'))
    ->addColumn(
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Entity Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'customer_segment_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Customer Segment Id'
    )
    ->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Product Set Id'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_related',
            [
                'entity_id',
                'store_id',
                'customer_group_id',
                'customer_segment_id'
            ]
        ),
        [
            'entity_id',
            'store_id',
            'customer_group_id',
            'customer_segment_id'
        ],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Enterprise Targetrule Index Related');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_upsell'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index_upsell'))
    ->addColumn(
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Entity Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'customer_segment_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Customer Segment Id'
    )
    ->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Product Set Id'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_upsell',
            [
                'entity_id',
                'store_id',
                'customer_group_id',
                'customer_segment_id'
            ]
        ),
        [
            'entity_id',
            'store_id',
            'customer_group_id',
            'customer_segment_id'
        ],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Enterprise Targetrule Index Upsell');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_crosssell'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index_crosssell'))
    ->addColumn(
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Entity Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Store Id'
    )
    ->addColumn(
        'customer_group_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Customer Group Id'
    )
    ->addColumn(
        'customer_segment_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Customer Segment Id'
    )
    ->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Product Set Id'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_crosssell',
            [
                'entity_id',
                'store_id',
                'customer_group_id',
                'customer_segment_id'
            ]
        ),
        [
            'entity_id',
            'store_id',
            'customer_group_id',
            'customer_segment_id'
        ],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->setComment('Enterprise Targetrule Index Crosssell');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_crosssell_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index_crosssell_product'))
    ->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
        ],
        'TargetRule Id'
    )
    ->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [
            'unsigned' => true,
            'nullable' => false,
        ],
        'Product Id'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_crosssell_product',
            ['product_set_id', 'product_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['product_set_id', 'product_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName(
            'magento_targetrule_index_crosssell_product',
            'product_set_id',
            'magento_targetrule_index_crosssell',
            'product_set_id'
        ),
        'product_set_id',
        $installer->getTable('magento_targetrule_index_crosssell'),
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Targetrule Index Crosssell Products');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_related_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index_related_product'))
    ->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
        ],
        'TargetRule Id'
    )
    ->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [
            'unsigned' => true,
            'nullable' => false,
        ],
        'Product Id'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_related_product',
            ['product_set_id', 'product_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['product_set_id', 'product_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName(
            'magento_targetrule_index_related_product',
            'product_set_id',
            'magento_targetrule_index_related',
            'product_set_id'
        ),
        'product_set_id',
        $installer->getTable('magento_targetrule_index_related'),
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Targetrule Index Related Products');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_targetrule_index_upsell_product'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_targetrule_index_upsell_product'))
    ->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
        ],
        'TargetRule Id'
    )
    ->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        [
            'unsigned' => true,
            'nullable' => false,
        ],
        'Product Id'
    )
    ->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_upsell_product',
            ['product_set_id', 'product_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        ['product_set_id', 'product_id'],
        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $installer->getFkName(
            'magento_targetrule_index_upsell_product',
            'product_set_id',
            'magento_targetrule_index_upsell',
            'product_set_id'
        ),
        'product_set_id',
        $installer->getTable('magento_targetrule_index_upsell'),
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Targetrule Index Upsell Products');

$installer->getConnection()->createTable($table);

$installer->endSetup();
