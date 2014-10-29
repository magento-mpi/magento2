<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order_status_history'),
    'entity_name',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 32,
        'nullable' => true,
        'comment' => 'Shows what entity history is bind to.'
    )
);
