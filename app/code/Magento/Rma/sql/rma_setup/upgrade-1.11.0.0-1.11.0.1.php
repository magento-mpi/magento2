<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Rma\Model\Resource\Setup */
$installer = $this;

/* adding new field = static attribute to rma_item_entity table */
$tableName = $installer->getTable('magento_rma_item_entity');
$columnOptions = array(
    'TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    'SCALE' => 4,
    'PRECISION' => 12,
    'COMMENT' => 'Qty of returned items'
);
$installer->getConnection()->addColumn($tableName, 'qty_returned', $columnOptions);
