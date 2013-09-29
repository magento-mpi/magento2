<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('sales_flat_order'), 'coupon_rule_name', array(
        'TYPE'      => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'LENGTH'    => 255,
        'NULLABLE'  => true,
        'COMMENT'   => 'Coupon Sales Rule Name'
    ));
