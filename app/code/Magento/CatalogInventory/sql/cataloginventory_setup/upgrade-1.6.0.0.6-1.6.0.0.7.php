<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Eav\Model\Entity\Setup */

$this->startSetup();
/**
 * Add new field to 'cataloginventory_stock_item'
 */
$this->getConnection()->addColumn(
    $this->getTable('cataloginventory_stock'),
    'website_id',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'LENGTH' => 5,
        'UNSIGNED' => true,
        'NULLABLE' => false,
        'COMMENT' => 'Website Id'
    )
);
$this->getConnection()->addIndex(
    $this->getTable('cataloginventory_stock'),
    $this->getIdxName(
        'cataloginventory_stock',
        array('website_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('website_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
);

$this->getConnection()->dropIndex(
    $this->getTable('cataloginventory_stock_item'),
    $this->getIdxName(
        'cataloginventory_stock_item',
        array('product_id', 'stock_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    )
);

$this->getConnection()->addIndex(
    $this->getTable('cataloginventory_stock_item'),
    $this->getIdxName(
        'cataloginventory_stock_item',
        array('product_id', 'website_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'website_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);
$this->endSetup();
