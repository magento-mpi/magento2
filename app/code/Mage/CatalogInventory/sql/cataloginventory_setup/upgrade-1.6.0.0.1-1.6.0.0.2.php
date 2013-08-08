<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

/**
 * Add new field to 'cataloginventory/stock_item'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('cataloginventory_stock_item'),
        'is_decimal_divided',
        array(
            'TYPE' => Magento_DB_Ddl_Table::TYPE_SMALLINT,
            'LENGTH' => 5,
            'UNSIGNED' => true,
            'NULLABLE' => false,
            'DEFAULT' => 0,
            'COMMENT' => 'Is Divided into Multiple Boxes for Shipping'
        )
    );
