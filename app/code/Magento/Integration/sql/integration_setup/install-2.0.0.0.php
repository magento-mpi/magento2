<?php
/**
 * Upgrade script for integration table creation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var \Magento\Core\Model\Resource\Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('integration'))
    ->addColumn(
        'integration_id',
        \Magento\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Integration ID'
    )
    ->addColumn(
        'name',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ),
        'Integration name is displayed in the admin interface'
    )
    ->addColumn(
        'status',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false
        ),
        'Integration status'
    )
    ->addColumn(
        'created_at',
        \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Creation Time'
    )
    ->addColumn(
        'updated_at',
        \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Update Time'
    )
    ->addIndex(
        $installer->getIdxName(
            'integration',
            array('name'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('name'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex(
        $installer->getIdxName(
            'integration',
            array('status'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
        ),
        array('status'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX)
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();
