<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->startSetup();

/**
 * Create table 'catalog_product_super_attribute'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('catalog_product_super_attribute')
)->addColumn(
    'product_super_attribute_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Product Super Attribute ID'
)->addColumn(
    'product_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Product ID'
)->addColumn(
    'attribute_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Attribute ID'
)->addColumn(
    'position',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Position'
)->addIndex(
    $installer->getIdxName('catalog_product_super_attribute', array('product_id')),
    array('product_id')
)->addIndex(
    $installer->getIdxName(
        'catalog_product_super_attribute',
        array('product_id', 'attribute_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'attribute_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addForeignKey(
    $installer->getFkName('catalog_product_super_attribute', 'product_id', 'catalog_product_entity', 'entity_id'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_NO_ACTION
)->setComment(
    'Catalog Product Super Attribute Table'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_super_attribute_label'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('catalog_product_super_attribute_label')
)->addColumn(
    'value_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Value ID'
)->addColumn(
    'product_super_attribute_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Product Super Attribute ID'
)->addColumn(
    'store_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Store ID'
)->addColumn(
    'use_default',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'default' => '0'),
    'Use Default Value'
)->addColumn(
    'value',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Value'
)->addIndex(
    $installer->getIdxName(
        'catalog_product_super_attribute_label',
        array('product_super_attribute_id', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_super_attribute_id', 'store_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addIndex(
    $installer->getIdxName('catalog_product_super_attribute_label', array('product_super_attribute_id')),
    array('product_super_attribute_id')
)->addIndex(
    $installer->getIdxName('catalog_product_super_attribute_label', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName(
        'catalog_product_super_attribute_label',
        'product_super_attribute_id',
        'catalog_product_super_attribute',
        'product_super_attribute_id'
    ),
    'product_super_attribute_id',
    $installer->getTable('catalog_product_super_attribute'),
    'product_super_attribute_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('catalog_product_super_attribute_label', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Catalog Product Super Attribute Label Table'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_super_attribute_pricing'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('catalog_product_super_attribute_pricing')
)->addColumn(
    'value_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Value ID'
)->addColumn(
    'product_super_attribute_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Product Super Attribute ID'
)->addColumn(
    'value_index',
    \Magento\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => true, 'default' => null),
    'Value Index'
)->addColumn(
    'is_percent',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'default' => '0'),
    'Is Percent'
)->addColumn(
    'pricing_value',
    \Magento\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array(),
    'Pricing Value'
)->addColumn(
    'website_id',
    \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Website ID'
)->addIndex(
    $installer->getIdxName('catalog_product_super_attribute_pricing', array('product_super_attribute_id')),
    array('product_super_attribute_id')
)->addIndex(
    $installer->getIdxName('catalog_product_super_attribute_pricing', array('website_id')),
    array('website_id')
)->addIndex(
    $installer->getIdxName(
        'catalog_product_super_attribute_pricing',
        array('product_super_attribute_id', 'value_index', 'website_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_super_attribute_id', 'value_index', 'website_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addForeignKey(
    $installer->getFkName('catalog_product_super_attribute_pricing', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName(
        'catalog_product_super_attribute_pricing',
        'product_super_attribute_id',
        'catalog_product_super_attribute',
        'product_super_attribute_id'
    ),
    'product_super_attribute_id',
    $installer->getTable('catalog_product_super_attribute'),
    'product_super_attribute_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Catalog Product Super Attribute Pricing Table'
);
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_super_link'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('catalog_product_super_link')
)->addColumn(
    'link_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Link ID'
)->addColumn(
    'product_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Product ID'
)->addColumn(
    'parent_id',
    \Magento\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
    'Parent ID'
)->addIndex(
    $installer->getIdxName('catalog_product_super_link', array('parent_id')),
    array('parent_id')
)->addIndex(
    $installer->getIdxName('catalog_product_super_link', array('product_id')),
    array('product_id')
)->addIndex(
    $installer->getIdxName(
        'catalog_product_super_link',
        array('product_id', 'parent_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('product_id', 'parent_id'),
    array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
)->addForeignKey(
    $installer->getFkName('catalog_product_super_link', 'product_id', 'catalog_product_entity', 'entity_id'),
    'product_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('catalog_product_super_link', 'parent_id', 'catalog_product_entity', 'entity_id'),
    'parent_id',
    $installer->getTable('catalog_product_entity'),
    'entity_id',
    \Magento\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Catalog Product Super Link Table'
);
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()->addColumn(
    $installer->getTable('catalog_eav_attribute'),
    'is_configurable',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'default' => null,
        'comment' => 'Can be used to create configurable product'
    )
);

$installer->endSetup();
