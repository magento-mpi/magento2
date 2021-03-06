<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

$installer = $this;
/* @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->startSetup();

/**
 * Create table 'magento_giftcard_amount'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_giftcard_amount'))
    ->addColumn(
        'value_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'nullable' => false, 'primary' => true],
        'Value Id'
    )
    ->addColumn(
        'website_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Website Id'
    )
    ->addColumn(
        'value',
        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
        '12,4',
        ['nullable' => false, 'default' => '0.0000'],
        'Value'
    )
    ->addColumn(
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Entity Id'
    )
    ->addColumn(
        'entity_type_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Entity Type Id'
    )
    ->addColumn(
        'attribute_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false],
        'Attribute Id'
    )
    ->addIndex(
        $installer->getIdxName('magento_giftcard_amount', ['entity_id']),
        ['entity_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_giftcard_amount', ['website_id']),
        ['website_id']
    )
    ->addIndex(
        $installer->getIdxName('magento_giftcard_amount', ['attribute_id']),
        ['attribute_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_giftcard_amount', 'entity_id', 'catalog_product_entity', 'entity_id'),
        'entity_id',
        $installer->getTable('catalog_product_entity'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_giftcard_amount', 'website_id', 'store_website', 'website_id'),
        'website_id',
        $installer->getTable('store_website'),
        'website_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_giftcard_amount', 'attribute_id', 'eav_attribute', 'attribute_id'),
        'attribute_id',
        $installer->getTable('eav_attribute'),
        'attribute_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Giftcard Amount');
$installer->getConnection()->createTable($table);

$installer->endSetup();
