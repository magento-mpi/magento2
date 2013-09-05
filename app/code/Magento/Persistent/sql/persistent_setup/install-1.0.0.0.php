<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'persistent_session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('persistent_session'))
    ->addColumn('persistent_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'primary'  => true,
        'nullable' => false,
        'unsigned' => true,
    ), 'Session id')
    ->addColumn('key', \Magento\DB\Ddl\Table::TYPE_TEXT, Magento_Persistent_Model_Session::KEY_LENGTH, array(
        'nullable' => false,
    ), 'Unique cookie key')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Customer id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website ID')
    ->addColumn('info', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(), 'Session Data')
    ->addColumn('updated_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Updated At')
    ->addIndex($installer->getIdxName('persistent_session', array('key')), array('key'), array(
        'type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE,
    ))
    ->addIndex($installer->getIdxName('persistent_session', array('customer_id')), array('customer_id'), array(
        'type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE,
    ))
    ->addIndex($installer->getIdxName('persistent_session', array('updated_at')), array('updated_at'))
    ->addForeignKey(
        $installer->getFkName('persistent_session', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer_entity'),
        'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('persistent_session', 'website_id', 'core_website', 'website_id'),
        'website_id',
        $installer->getTable('core_website'),
        'website_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Persistent Session');

$installer->getConnection()->createTable($table);

/**
 * Alter sales_flat_quote table with is_persistent flag
 *
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales_flat_quote'),
        'is_persistent',
        array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'unsigned' => true,
            'default'  => '0',
            'comment'  => 'Is Quote Persistent',
        )
    );

$installer->endSetup();
