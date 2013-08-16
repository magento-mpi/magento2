<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Mage_Tax_Model_Resource_Setup */

/**
 * Add new field to 'sales_order_tax_item'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_order_tax_item'),
        'tax_percent',
        array(
            'TYPE'      => Magento_DB_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'NULLABLE'  => false,
            'COMMENT'   => 'Real Tax Percent For Item',
        )
    );
