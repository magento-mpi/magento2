<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento\Setup\Module\SetupModule */

$this->getConnection()->changeColumn(
    $this->getTable('cataloginventory_stock_item'),
    'qty',
    'qty',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        'LENGTH' => '12,4',
        'UNSIGNED' => false,
        'NULLABLE' => true,
        'DEFAULT' => null,
        'COMMENT' => 'Qty'
    )
);
