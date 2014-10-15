<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('catalogrule_product');
$installer->getConnection()->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product', 'product_id', 'catalog_product_entity', 'entity_id')
)->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product', 'customer_group_id', 'customer_group', 'customer_group_id')
)->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product', 'website_id', 'store_website', 'website_id')
)->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product', 'rule_id', 'catalogrule', 'rule_id')
);

$table = $installer->getConnection()->newTable(
    $installer->getTable('catalogrule_category_tmp')
)->addColumn(
    'rule_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Rule Id'
)->addColumn(
    'category_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Category Id'
)->addIndex(
    $installer->getIdxName('catalogrule_website', array('category_id')),
    array('category_id')
)->setComment(
    'Catalog Rules To Category Relations'
)->setOption(
    'type',
    \Magento\Framework\DB\Adapter\Pdo\Mysql::ENGINE_MEMORY
);

$installer->getConnection()->createTable($table);

$installer->endSetup();
