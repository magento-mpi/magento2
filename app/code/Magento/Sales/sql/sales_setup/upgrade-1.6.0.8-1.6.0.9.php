<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->modifyColumn($installer->getTable('sales_flat_quote_payment'), 'cc_exp_year',
        array(
            'type'      => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length'      => 255,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Cc Exp Year'
        )
    )->modifyColumn($installer->getTable('sales_flat_quote_payment'), 'cc_exp_month',
        array(
            'type'      => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'length'      => 255,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Cc Exp Month'
        )
    );

$installer->endSetup();
