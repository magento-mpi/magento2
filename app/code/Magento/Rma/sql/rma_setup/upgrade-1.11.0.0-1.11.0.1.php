<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Rma_Model_Resource_Setup */
$installer = $this;

/* adding new field = static attribute to rma_item_entity table */
$tableName = $installer->getTable('magento_rma_item_entity');
$columnOptions = array(
    'TYPE' => Magento_DB_Ddl_Table::TYPE_DECIMAL,
    'SCALE' => 4,
    'PRECISION' => 12,
    'COMMENT' => 'Qty of returned items',
);
$installer->getConnection()
    ->addColumn($tableName, 'qty_returned', $columnOptions);

$installer->addAttribute('rma_item', 'qty_returned', array(
            'type'               => 'static',
            'label'              => 'Qty of returned items',
            'input'              => 'text',
            'visible'            => false,
            'sort_order'         => 45,
            'position'           => 45,
));
