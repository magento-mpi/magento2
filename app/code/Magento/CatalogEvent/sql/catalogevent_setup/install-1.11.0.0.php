<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_CatalogEvent_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'magento_catalogevent_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogevent_event'))
    ->addColumn('event_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Id')
    ->addColumn('category_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Category Id')
    ->addColumn('date_start', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Date Start')
    ->addColumn('date_end', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Date End')
    ->addColumn('display_state', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Display State')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('magento_catalogevent_event', array('category_id'), true),
        array('category_id'), array('type' => 'unique'))
    ->addIndex($installer->getIdxName('magento_catalogevent_event', array('date_start', 'date_end')),
        array('date_start', 'date_end'))
    ->addForeignKey($installer->getFkName('magento_catalogevent_event', 'category_id', 'catalog_category_entity', 'entity_id'),
        'category_id', $installer->getTable('catalog_category_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Catalogevent Event');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_catalogevent_event_image'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_catalogevent_event_image'))
    ->addColumn('event_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addColumn('image', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Image')
    ->addIndex($installer->getIdxName('magento_catalogevent_event_image', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('magento_catalogevent_event_image', 'event_id', 'magento_catalogevent_event', 'event_id'),
        'event_id', $installer->getTable('magento_catalogevent_event'), 'event_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_catalogevent_event_image', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Catalogevent Event Image');
$installer->getConnection()->createTable($table);

$installer->addAttribute('quote_item', 'event_id', array('type' => \Magento\DB\Ddl\Table::TYPE_INTEGER));
$installer->addAttribute('order_item', 'event_id', array('type' => \Magento\DB\Ddl\Table::TYPE_INTEGER));

$installer->endSetup();
