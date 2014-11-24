<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

/**
 * Add new field to 'cataloginventory/stock_item'
 */
$installer->getConnection()->addColumn(
    $installer->getTable('cataloginventory_stock_item'),
    'is_decimal_divided',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'LENGTH' => 5,
        'UNSIGNED' => true,
        'NULLABLE' => false,
        'DEFAULT' => 0,
        'COMMENT' => 'Is Divided into Multiple Boxes for Shipping'
    )
);
