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
    $this->getTable('cataloginventory_stock_item'),
    'website_id',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'LENGTH' => 5,
        'UNSIGNED' => true,
        'NULLABLE' => false,
        'DEFAULT' => 0,
        'COMMENT' => 'Is Divided into Multiple Boxes for Shipping'
    )
);
$this->getConnection()->addIndex(
    $this->getTable('cataloginventory_stock_item'),
    $this->getIdxName(
        'cataloginventory_stock_item',
        array('website_id'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
    ),
    array('website_id'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
);
$this->endSetup();
