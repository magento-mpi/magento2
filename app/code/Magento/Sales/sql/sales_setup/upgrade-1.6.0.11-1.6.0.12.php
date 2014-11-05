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

$installer->getConnection()->addColumn(
    $installer->getTable('sales_order_status_state'),
    'visible_on_front',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => false,
        'default' => 0,
        'comment' => 'visible_on_front'
    )
);

$installer->endSetup();
