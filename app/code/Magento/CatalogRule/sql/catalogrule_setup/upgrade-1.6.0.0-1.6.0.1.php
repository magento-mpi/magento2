<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$tableName = $installer->getTable('catalogrule');
$columnOptions = array(
    'TYPE' => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    'UNSIGNED' => true,
    'NULLABLE' => false,
    'DEFAULT' => 0,
    'COMMENT' => 'Is Rule Enable For Subitems'
);
$installer->getConnection()->addColumn($tableName, 'sub_is_enable', $columnOptions);

$columnOptions = array(
    'TYPE' => \Magento\DB\Ddl\Table::TYPE_TEXT,
    'LENGTH' => 32,
    'COMMENT' => 'Simple Action For Subitems'
);
$installer->getConnection()->addColumn($tableName, 'sub_simple_action', $columnOptions);

$columnOptions = array(
    'TYPE' => \Magento\DB\Ddl\Table::TYPE_DECIMAL,
    'SCALE' => 4,
    'PRECISION' => 12,
    'NULLABLE' => false,
    'DEFAULT' => '0.0000',
    'COMMENT' => 'Discount Amount For Subitems'
);
$installer->getConnection()->addColumn($tableName, 'sub_discount_amount', $columnOptions);
