<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$tableName = $installer->getTable('magento_rma_item_entity');

$installer->getConnection()->addColumn(
    $tableName,
    'product_admin_name',
    array('TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'LENGTH' => 255, 'COMMENT' => 'Product Name For Backend')
);
$installer->getConnection()->addColumn(
    $tableName,
    'product_admin_sku',
    array('TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'LENGTH' => 255, 'COMMENT' => 'Product Sku For Backend')
);
$installer->getConnection()->addColumn(
    $tableName,
    'product_options',
    array('TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'COMMENT' => 'Product Options')
);
