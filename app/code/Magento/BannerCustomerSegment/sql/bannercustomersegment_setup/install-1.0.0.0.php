<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$tableName = 'magento_banner_customersegment';

$table = $installer->getConnection()->newTable(
    $installer->getTable($tableName)
)->addColumn(
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
    'Banner Id'
)->addColumn(
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
    'Segment Id'
)->addIndex(
    $installer->getIdxName($tableName, ['segment_id']),
    ['segment_id']
)->addForeignKey(
    $installer->getFkName($tableName, 'banner_id', 'magento_banner', 'banner_id'),
    'banner_id',
    $installer->getTable('magento_banner'),
    'banner_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName($tableName, 'segment_id', 'magento_customersegment_segment', 'segment_id'),
    'segment_id',
    $installer->getTable('magento_customersegment_segment'),
    'segment_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Banner Customersegment'
);

// Table used to be part of the Magento_Banner module, so during upgrade it may exist already
if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}
