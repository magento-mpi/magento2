<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->modifyColumn($installer->getTable('sales_flat_quote_payment'), 'cc_exp_year',
        array(
            'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
            'length'      => 255,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Cc Exp Year'
        )
    )->modifyColumn($installer->getTable('sales_flat_quote_payment'), 'cc_exp_month',
        array(
            'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
            'length'      => 255,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Cc Exp Month'
        )
    );

$installer->endSetup();
