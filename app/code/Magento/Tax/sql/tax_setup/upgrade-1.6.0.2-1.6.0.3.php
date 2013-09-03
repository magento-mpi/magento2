<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Magento_Tax_Model_Resource_Setup */

/**
 * Add new field to 'sales_order_tax_item'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_order_tax_item'),
        'tax_percent',
        array(
            'TYPE'      => \Magento\DB\Ddl\Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'NULLABLE'  => false,
            'COMMENT'   => 'Real Tax Percent For Item',
        )
    );
