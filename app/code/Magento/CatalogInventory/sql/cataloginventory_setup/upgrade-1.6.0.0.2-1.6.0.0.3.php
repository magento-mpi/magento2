<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Magento_Eav_Model_Entity_Setup */

$this->getConnection()
    ->changeColumn(
        $this->getTable('cataloginventory_stock_item'),
        'qty',
        'qty',
        array(
            'TYPE' => \Magento\DB\Ddl\Table::TYPE_DECIMAL,
            'LENGTH' => '12,4',
            'UNSIGNED' => false,
            'NULLABLE' => true,
            'DEFAULT' => null,
            'COMMENT' => 'Qty'
        )
    );
