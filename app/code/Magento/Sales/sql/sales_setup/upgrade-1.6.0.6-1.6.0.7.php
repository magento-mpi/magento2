<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order'),
    'coupon_rule_name',
    array(
        'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'LENGTH' => 255,
        'NULLABLE' => true,
        'COMMENT' => 'Coupon Sales Rule Name'
    )
);
