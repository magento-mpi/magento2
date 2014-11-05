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

$installer->getConnection()->addColumn(
    $installer->getTable('magento_targetrule_index'),
    'customer_segment_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Customer Segment Id'
    )
);

$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index'),
    $installer->getConnection()->getPrimaryKeyName($installer->getTable('magento_targetrule_index')),
    array('entity_id', 'store_id', 'customer_group_id', 'type_id', 'customer_segment_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addColumn(
    $installer->getTable('magento_targetrule_index_related'),
    'customer_segment_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Customer Segment Id'
    )
);
$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index_related'),
    $installer->getConnection()->getPrimaryKeyName($installer->getTable('magento_targetrule_index_related')),
    array('entity_id', 'store_id', 'customer_group_id', 'customer_segment_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addColumn(
    $installer->getTable('magento_targetrule_index_upsell'),
    'customer_segment_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Customer Segment Id'
    )
);
$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index_upsell'),
    $installer->getConnection()->getPrimaryKeyName($installer->getTable('magento_targetrule_index_upsell')),
    array('entity_id', 'store_id', 'customer_group_id', 'customer_segment_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addColumn(
    $installer->getTable('magento_targetrule_index_crosssell'),
    'customer_segment_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Customer Segment Id'
    )
);
$installer->getConnection()->addIndex(
    $installer->getTable('magento_targetrule_index_crosssell'),
    $installer->getConnection()->getPrimaryKeyName($installer->getTable('magento_targetrule_index_crosssell')),
    array('entity_id', 'store_id', 'customer_group_id', 'customer_segment_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
);

$installer->endSetup();
