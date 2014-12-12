<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $installer \Magento\CatalogEvent\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'magento_catalogevent_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogevent_event'))
    ->addColumn(
        'event_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Event Id'
    )
    ->addColumn(
        'category_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true],
        'Category Id'
    )
    ->addColumn(
        'date_start',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        [],
        'Date Start'
    )
    ->addColumn(
        'date_end',
        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        [],
        'Date End'
    )
    ->addColumn(
        'display_state',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'default' => '0'],
        'Display State'
    )
    ->addColumn(
        'sort_order',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true],
        'Sort Order'
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogevent_event', ['category_id'], true),
        ['category_id'],
        ['type' => 'unique']
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogevent_event', ['date_start', 'date_end']),
        ['date_start', 'date_end']
    )
    ->addForeignKey(
        $installer->getFkName('magento_catalogevent_event', 'category_id', 'catalog_category_entity', 'entity_id'),
        'category_id',
        $installer->getTable('catalog_category_entity'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Catalogevent Event');

$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogevent_event_image'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogevent_event_image'))
    ->addColumn(
        'event_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Event Id'
    )
    ->addColumn(
        'store_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'primary' => true],
        'Store Id'
    )
    ->addColumn(
        'image',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        ['nullable' => false],
        'Image'
    )
    ->addIndex(
        $installer->getIdxName('magento_catalogevent_event_image', ['store_id']),
        ['store_id']
    )
    ->addForeignKey(
        $installer->getFkName('magento_catalogevent_event_image', 'event_id', 'magento_catalogevent_event', 'event_id'),
        'event_id',
        $installer->getTable('magento_catalogevent_event'),
        'event_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('magento_catalogevent_event_image', 'store_id', 'store', 'store_id'),
        'store_id',
        $installer->getTable('store'),
        'store_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Enterprise Catalogevent Event Image');

$installer->getConnection()->createTable($table);

$installer->endSetup();
