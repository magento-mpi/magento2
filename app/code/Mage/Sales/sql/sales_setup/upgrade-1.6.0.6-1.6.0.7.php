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

$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order'), 'coupon_rule_name', array(
        'TYPE'      => Magento_DB_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => 255,
        'NULLABLE'  => true,
        'COMMENT'   => 'Coupon Sales Rule Name'
    ));
