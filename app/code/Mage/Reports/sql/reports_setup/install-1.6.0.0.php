<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
/*
 * Prepare database for tables install
 */
$installer->startSetup();
/**
 * Create table 'report_event_types'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('report_event_types'))
    ->addColumn('event_type_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Type Id')
    ->addColumn('event_name', Magento_DB_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => false,
        ), 'Event Name')
    ->addColumn('customer_login', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Login')
    ->setComment('Reports Event Type Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'report_event'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('report_event'))
    ->addColumn('event_id', Magento_DB_Ddl_Table::TYPE_BIGINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Event Id')
    ->addColumn('logged_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Logged At')
    ->addColumn('event_type_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Event Type Id')
    ->addColumn('object_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Object Id')
    ->addColumn('subject_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Subject Id')
    ->addColumn('subtype', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Subtype')
    ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addIndex($installer->getIdxName('report_event', array('event_type_id')),
        array('event_type_id'))
    ->addIndex($installer->getIdxName('report_event', array('subject_id')),
        array('subject_id'))
    ->addIndex($installer->getIdxName('report_event', array('object_id')),
        array('object_id'))
    ->addIndex($installer->getIdxName('report_event', array('subtype')),
        array('subtype'))
    ->addIndex($installer->getIdxName('report_event', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('report_event', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('report_event', 'event_type_id', 'report_event_types', 'event_type_id'),
        'event_type_id', $installer->getTable('report_event_types'), 'event_type_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Reports Event Table');
$installer->getConnection()->createTable($table);


/**
 * Create table 'report_compared_product_index'.
 * MySQL table differs by having unique keys on (customer/visitor, product) columns and is created
 * in separate install.
 */
$tableName = $installer->getTable('report_compared_product_index');
if (!$installer->tableExists($tableName)) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('index_id', Magento_DB_Ddl_Table::TYPE_BIGINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Index Id')
        ->addColumn('visitor_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Visitor Id')
        ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Customer Id')
        ->addColumn('product_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Id')
        ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            ), 'Store Id')
        ->addColumn('added_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            ), 'Added At')
        ->addIndex($installer->getIdxName('report_compared_product_index', array('visitor_id', 'product_id')),
            array('visitor_id', 'product_id'))
        ->addIndex($installer->getIdxName('report_compared_product_index', array('customer_id', 'product_id')),
            array('customer_id', 'product_id'))
        ->addIndex($installer->getIdxName('report_compared_product_index', array('store_id')),
            array('store_id'))
        ->addIndex($installer->getIdxName('report_compared_product_index', array('added_at')),
            array('added_at'))
        ->addIndex($installer->getIdxName('report_compared_product_index', array('product_id')),
            array('product_id'))
        ->addForeignKey($installer->getFkName('report_compared_product_index', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id', $installer->getTable('customer_entity'), 'entity_id',
            Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('report_compared_product_index', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
            Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('report_compared_product_index', 'store_id', 'core_store', 'store_id'),
            'store_id', $installer->getTable('core_store'), 'store_id',
            Magento_DB_Ddl_Table::ACTION_SET_NULL, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->setComment('Reports Compared Product Index Table');
    $installer->getConnection()->createTable($table);
}


/**
 * Create table 'report_viewed_product_index'.
 * MySQL table differs by having unique keys on (customer/visitor, product) columns and is created
 * in separate install.
 */
$tableName = $installer->getTable('report_viewed_product_index');
if (!$installer->tableExists($tableName)) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('index_id', Magento_DB_Ddl_Table::TYPE_BIGINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Index Id')
        ->addColumn('visitor_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Visitor Id')
        ->addColumn('customer_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Customer Id')
        ->addColumn('product_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product Id')
        ->addColumn('store_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            ), 'Store Id')
        ->addColumn('added_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable'  => false,
            ), 'Added At')
        ->addIndex($installer->getIdxName('report_viewed_product_index', array('visitor_id', 'product_id')),
            array('visitor_id', 'product_id'))
        ->addIndex($installer->getIdxName('report_viewed_product_index', array('customer_id', 'product_id')),
            array('customer_id', 'product_id'))
        ->addIndex($installer->getIdxName('report_viewed_product_index', array('store_id')),
            array('store_id'))
        ->addIndex($installer->getIdxName('report_viewed_product_index', array('added_at')),
            array('added_at'))
        ->addIndex($installer->getIdxName('report_viewed_product_index', array('product_id')),
            array('product_id'))
        ->addForeignKey($installer->getFkName('report_viewed_product_index', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id', $installer->getTable('customer_entity'), 'entity_id',
            Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('report_viewed_product_index', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
            Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('report_viewed_product_index', 'store_id', 'core_store', 'store_id'),
            'store_id', $installer->getTable('core_store'), 'store_id',
            Magento_DB_Ddl_Table::ACTION_SET_NULL, Magento_DB_Ddl_Table::ACTION_CASCADE)
        ->setComment('Reports Viewed Product Index Table');
    $installer->getConnection()->createTable($table);
}

/*
 * Prepare database for tables install
 */
$installer->endSetup();

