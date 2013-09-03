<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @var $installer Magento_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule_coupon'),
        'created_at',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
            'comment'  => 'Coupon Code Creation Date',
            'nullable' => false,
            'default'  => \Magento\DB\Ddl\Table::TIMESTAMP_INIT
        )
    );

$installer->getConnection()->addColumn(
        $installer->getTable('salesrule_coupon'),
        'type',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'comment'  => 'Coupon Code Type',
            'default'  => 0
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule'),
        'use_auto_generation',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'comment'  => 'Use Auto Generation',
            'nullable' => false,
            'default'  => 0
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('salesrule'),
        'uses_per_coupon',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_INTEGER,
            'comment'  => 'Uses Per Coupon',
            'nullable' => false,
            'default'  => 0
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('coupon_aggregated'),
        'rule_name',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('coupon_aggregated_order'),
        'rule_name',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        )
    );

$installer->getConnection()
    ->addColumn(
        $installer->getTable('coupon_aggregated_updated'),
        'rule_name',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 255,
            'comment'  => 'Rule Name',
        )
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('coupon_aggregated'),
        $installer->getIdxName(
            'coupon_aggregated',
            array('rule_name'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
        ),
        array('rule_name'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('coupon_aggregated_order'),
        $installer->getIdxName(
            'coupon_aggregated_order',
            array('rule_name'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
        ),
        array('rule_name'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    );

$installer->getConnection()
    ->addIndex(
        $installer->getTable('coupon_aggregated_updated'),
        $installer->getIdxName(
            'coupon_aggregated_updated',
            array('rule_name'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
        ),
        array('rule_name'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    );
