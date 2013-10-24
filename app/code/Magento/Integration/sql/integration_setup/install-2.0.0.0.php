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
        'email',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ),
        'Email address of the contact person'
    )
    ->addColumn(
        'authentication',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false
        ),
        'Authentication mechanism'
    )
    ->addColumn(
        'endpoint',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
        ),
        'Endpoint for Oauth handshake'
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
        array('default' => \Magento\DB\Ddl\Table::TIMESTAMP_INIT),
        'Creation Time'
    )
    ->addColumn(
        'updated_at',
        \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
        null,
        array('default' => \Magento\DB\Ddl\Table::TIMESTAMP_UPDATE),
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
    );
$installer->getConnection()->createTable($table);

$installer->endSetup();
