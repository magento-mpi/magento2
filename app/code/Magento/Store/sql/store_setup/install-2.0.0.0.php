<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use \Magento\DB\Ddl\Table;

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Create table 'core_website'
 */
$table = $connection
    ->newTable($installer->getTable('core_website'))
    ->addColumn(
        'website_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Website Id'
    )
    ->addColumn('code', Table::TYPE_TEXT, 32, array(), 'Code')
    ->addColumn('name', Table::TYPE_TEXT, 64, array(), 'Website Name')
    ->addColumn(
        'sort_order',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Sort Order'
    )
    ->addColumn(
        'default_group_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Default Group Id'
    )
    ->addColumn(
        'is_default',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'default' => '0',
        ),
        'Defines Is Website Default'
    )
    ->addIndex(
        $installer->getIdxName('core_website', array('code'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('code'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('core_website', array('sort_order')),
        array('sort_order')
    )
    ->addIndex(
        $installer->getIdxName('core_website', array('default_group_id')),
        array('default_group_id')
    )
    ->setComment('Websites');
$connection->createTable($table);

/**
 * Create table 'core_store_group'
 */
$table = $connection
    ->newTable($installer->getTable('core_store_group'))
    ->addColumn(
        'group_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Group Id'
    )
    ->addColumn(
        'website_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Website Id'
    )
    ->addColumn(
        'name',
        Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ),
        'Store Group Name'
    )
    ->addColumn(
        'root_category_id',
        Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Root Category Id'
    )
    ->addColumn(
        'default_store_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Default Store Id'
    )
    ->addIndex(
        $installer->getIdxName('core_store_group', array('website_id')),
        array('website_id')
    )
    ->addIndex(
        $installer->getIdxName('core_store_group', array('default_store_id')),
        array('default_store_id')
    )
    ->addForeignKey(
        $installer->getFkName('core_store_group', 'website_id', 'core_website', 'website_id'),
        'website_id',
        $installer->getTable('core_website'),
        'website_id',
        Table::ACTION_CASCADE,
        Table::ACTION_CASCADE
    )
    ->setComment('Store Groups');
$connection->createTable($table);

/**
 * Create table 'core_store'
 */
$table = $connection
    ->newTable($installer->getTable('core_store'))
    ->addColumn(
        'store_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Store Id'
    )
    ->addColumn('code', Table::TYPE_TEXT, 32, array(), 'Code')
    ->addColumn(
        'website_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Website Id'
    )
    ->addColumn(
        'group_id',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Group Id'
    )
    ->addColumn(
        'name',
        Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ),
        'Store Name'
    )
    ->addColumn(
        'sort_order',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Store Sort Order'
    )
    ->addColumn(
        'is_active',
        Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ),
        'Store Activity'
    )
    ->addIndex(
        $installer->getIdxName('core_store', array('code'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('code'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName('core_store', array('website_id')),
        array('website_id')
    )
    ->addIndex(
        $installer->getIdxName('core_store', array('is_active', 'sort_order')),
        array('is_active', 'sort_order')
    )
    ->addIndex(
        $installer->getIdxName('core_store', array('group_id')),
        array('group_id')
    )
    ->addForeignKey(
        $installer->getFkName('core_store', 'group_id', 'core_store_group', 'group_id'),
        'group_id',
        $installer->getTable('core_store_group'),
        'group_id',
        Table::ACTION_CASCADE,
        Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('core_store', 'website_id', 'core_website', 'website_id'),
        'website_id',
        $installer->getTable('core_website'),
        'website_id',
        Table::ACTION_CASCADE,
        Table::ACTION_CASCADE
    )
    ->setComment('Stores');
$connection
    ->createTable($table);

/**
 * Insert core websites
 */
$connection
    ->insertForce(
        $installer->getTable('core_website'),
        array(
            'website_id' => 0,
            'code' => 'admin',
            'name' => 'Admin',
            'sort_order' => 0,
            'default_group_id' => 0,
            'is_default' => 0,
        )
    );
$connection
    ->insertForce(
        $installer->getTable('core_website'),
        array(
            'website_id' => 1,
            'code' => 'base',
            'name' => 'Main Website',
            'sort_order' => 0,
            'default_group_id' => 1,
            'is_default' => 1,
        )
    );

/**
 * Insert core store groups
 */
$connection
    ->insertForce(
        $installer->getTable('core_store_group'),
        array(
            'group_id' => 0,
            'website_id' => 0,
            'name' => 'Default',
            'root_category_id' => 0,
            'default_store_id' => 0
        )
    );
$connection
    ->insertForce(
        $installer->getTable('core_store_group'),
        array(
            'group_id' => 1,
            'website_id' => 1,
            'name' => 'Main Website Store',
            'root_category_id' => 2,
            'default_store_id' => 1
        )
    );

/**
 * Insert core stores
 */
$connection
    ->insertForce(
        $installer->getTable('core_store'),
        array(
            'store_id' => 0,
            'code' => 'admin',
            'website_id' => 0,
            'group_id' => 0,
            'name' => 'Admin',
            'sort_order' => 0,
            'is_active' => 1,
        )
    );
$connection
    ->insertForce(
        $installer->getTable('core_store'),
        array(
            'store_id' => 1,
            'code' => 'default',
            'website_id' => 1,
            'group_id' => 1,
            'name' => 'Default Store View',
            'sort_order' => 0,
            'is_active' => 1,
        )
    );

$installer->endSetup();

