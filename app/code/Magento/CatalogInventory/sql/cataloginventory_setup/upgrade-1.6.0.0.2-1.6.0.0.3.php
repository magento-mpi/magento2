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
            'TYPE' => Magento_DB_Ddl_Table::TYPE_DECIMAL,
            'LENGTH' => '12,4',
            'UNSIGNED' => false,
            'NULLABLE' => true,
            'DEFAULT' => null,
            'COMMENT' => 'Qty'
        )
    );
