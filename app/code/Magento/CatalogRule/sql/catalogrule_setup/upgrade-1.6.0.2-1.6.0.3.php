<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

$ruleProductTable = $installer->getTable('catalogrule_product');

$columnOptions = array(
    'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    'LENGTH' => 32,
    'COMMENT' => 'Simple Action For Subitems'
);
$installer->getConnection()->addColumn($ruleProductTable, 'sub_simple_action', $columnOptions);

$columnOptions = array(
    'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    'SCALE' => 4,
    'PRECISION' => 12,
    'NULLABLE' => false,
    'DEFAULT' => '0.0000',
    'COMMENT' => 'Discount Amount For Subitems'
);
$installer->getConnection()->addColumn($ruleProductTable, 'sub_discount_amount', $columnOptions);

$installer->endSetup();
