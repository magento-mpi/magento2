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

$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getFkName(
        'magento_targetrule_index_crosssell',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getFkName(
        'magento_targetrule_index_crosssell',
        'entity_id',
        'catalog_product_entity',
        'entity_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getFkName(
        'magento_targetrule_index_crosssell',
        'store_id',
        'store',
        'store_id'
    )
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getFkName(
        'magento_targetrule_index_related',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getFkName(
        'magento_targetrule_index_related',
        'entity_id',
        'catalog_product_entity',
        'entity_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getFkName(
        'magento_targetrule_index_related',
        'store_id',
        'store',
        'store_id'
    )
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getFkName(
        'magento_targetrule_index_upsell',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getFkName(
        'magento_targetrule_index_upsell',
        'entity_id',
        'catalog_product_entity',
        'entity_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getFkName(
        'magento_targetrule_index_upsell',
        'store_id',
        'store',
        'store_id'
    )
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index'),
    $installer->getFkName(
        'magento_targetrule_index',
        'customer_group_id',
        'customer_group',
        'customer_group_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index'),
    $installer->getFkName(
        'magento_targetrule_index',
        'entity_id',
        'catalog_product_entity',
        'entity_id'
    )
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('magento_targetrule_index'),
    $installer->getFkName(
        'magento_targetrule_index',
        'store_id',
        'store',
        'store_id'
    )
);

$installer->getConnection()->dropIndex($installer->getTable('magento_targetrule_index_crosssell'), 'PRIMARY');
$installer->getConnection()->dropIndex($installer->getTable('magento_targetrule_index_related'), 'PRIMARY');
$installer->getConnection()->dropIndex($installer->getTable('magento_targetrule_index_upsell'), 'PRIMARY');

$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getIdxName('magento_targetrule_index_crosssell', array('store_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getIdxName('magento_targetrule_index_crosssell', array('group_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getIdxName('magento_targetrule_index_crosssell', array('customer_group_id'))
);

$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getIdxName('magento_targetrule_index_related', array('store_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getIdxName('magento_targetrule_index_related', array('group_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getIdxName('magento_targetrule_index_related', array('customer_group_id'))
);

$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getIdxName('magento_targetrule_index_upsell', array('store_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getIdxName('magento_targetrule_index_upsell', array('group_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getIdxName('magento_targetrule_index_upsell', array('customer_group_id'))
);

$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index'),
    $installer->getIdxName('magento_targetrule_index', array('store_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index'),
    $installer->getIdxName('magento_targetrule_index', array('group_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index'),
    $installer->getIdxName('magento_targetrule_index', array('type_id'))
);
$installer->getConnection()->dropIndex(
    $installer->getTable('magento_targetrule_index'),
    $installer->getIdxName('magento_targetrule_index', array('customer_group_id'))
);

$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getIdxName(
        'magento_targetrule_index_crosssell',
        array(
            'entity_id',
            'store_id',
            'customer_group_id',
            'customer_segment_id'
        )
    ),
    array(
        'entity_id',
        'store_id',
        'customer_group_id',
        'customer_segment_id'
    ),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getIdxName(
        'magento_targetrule_index_related',
        array(
            'entity_id',
            'store_id',
            'customer_group_id',
            'customer_segment_id'
        )
    ),
    array(
        'entity_id',
        'store_id',
        'customer_group_id',
        'customer_segment_id'
    ),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getIdxName(
        'magento_targetrule_index_upsell',
        array(
            'entity_id',
            'store_id',
            'customer_group_id',
            'customer_segment_id'
        )
    ),
    array(
        'entity_id',
        'store_id',
        'customer_group_id',
        'customer_segment_id'
    ),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()
    ->dropColumn(
        $installer->getTable('magento_targetrule_index_crosssell'),
        'product_ids'
    );

$installer->getConnection()
    ->dropColumn(
        $installer->getTable('magento_targetrule_index_related'),
        'product_ids'
    );

$installer->getConnection()
    ->dropColumn(
        $installer->getTable('magento_targetrule_index_upsell'),
        'product_ids'
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_targetrule_index_crosssell'),
        'product_set_id',
        array(
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'comment' => 'Product Set Id'
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_targetrule_index_related'),
        'product_set_id',
        array(
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'comment' => 'Product Set Id'
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('magento_targetrule_index_upsell'),
        'product_set_id',
        array(
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'comment' => 'Product Set Id'
        )
    );

$table = $installer->getConnection()
    ->newTable(
        $installer->getTable('magento_targetrule_index_crosssell_product')
    )->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'TargetRule Id'
    )->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'Product Id'
    )->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_crosssell_product',
            array('product_set_id', 'product_id'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('product_set_id', 'product_id'),
        array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )->addForeignKey(
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
    )->setComment(
        'Enterprise Targetrule Index Crosssell Products'
    );

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable(
        $installer->getTable('magento_targetrule_index_related_product')
    )->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'TargetRule Id'
    )->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'Product Id'
    )->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_related_product',
            array('product_set_id', 'product_id'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('product_set_id', 'product_id'),
        array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )->addForeignKey(
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
    )->setComment(
        'Enterprise Targetrule Index Related Products'
    );

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable(
        $installer->getTable('magento_targetrule_index_upsell_product')
    )->addColumn(
        'product_set_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'TargetRule Id'
    )->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'Product Id'
    )->addIndex(
        $installer->getIdxName(
            'magento_targetrule_index_upsell_product',
            array('product_set_id', 'product_id'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('product_set_id', 'product_id'),
        array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )->addForeignKey(
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
    )->setComment(
        'Enterprise Targetrule Index Upsell Products'
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
