<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();
/**
 * Create table 'weee_tax'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('weee_tax')
)->addColumn(
    'value_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'nullable' => false, 'primary' => true),
    'Value Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Website Id'
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'country',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    2,
    array('nullable' => true),
    'Country'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Value'
)->addColumn(
    'state',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false, 'default' => '*'),
    'State'
)->addColumn(
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Attribute Id'
)->addColumn(
    'entity_type_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Entity Type Id'
)->addIndex(
    $installer->getIdxName('weee_tax', array('website_id')),
    array('website_id')
)->addIndex(
    $installer->getIdxName('weee_tax', array('entity_id')),
    array('entity_id')
)->addIndex(
    $installer->getIdxName('weee_tax', array('country')),
    array('country')
)->addIndex(
    $installer->getIdxName('weee_tax', array('attribute_id')),
    array('attribute_id')
)->addForeignKey(
    $installer->getFkName('weee_tax', 'country', 'directory_country', 'country_id'),
    'country',
    $installer->getTable('directory_country'),
    'country_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('weee_tax', 'entity_id', 'catalog_product_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('weee_tax', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('weee_tax', 'attribute_id', 'eav_attribute', 'attribute_id'),
    'attribute_id',
    $installer->getTable('eav_attribute'),
    'attribute_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Weee Tax'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'weee_discount'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('weee_discount')
)->addColumn(
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Entity Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Website Id'
)->addColumn(
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Customer Group Id'
)->addColumn(
    'value',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false, 'default' => '0.0000'),
    'Value'
)->addIndex(
    $installer->getIdxName('weee_discount', array('website_id')),
    array('website_id')
)->addIndex(
    $installer->getIdxName('weee_discount', array('entity_id')),
    array('entity_id')
)->addIndex(
    $installer->getIdxName('weee_discount', array('customer_group_id')),
    array('customer_group_id')
)->addForeignKey(
    $installer->getFkName('weee_discount', 'customer_group_id', 'customer_group', 'customer_group_id'),
    'customer_group_id',
    $installer->getTable('customer_group'),
    'customer_group_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('weee_discount', 'entity_id', 'catalog_product_entity', 'entity_id'),
    'entity_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('weee_discount', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Weee Discount'
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
