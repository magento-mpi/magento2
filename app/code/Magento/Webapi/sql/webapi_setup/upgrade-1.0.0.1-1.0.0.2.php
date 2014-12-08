<?php
/**
 * Update script for Webapi module.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('webapi_user');

$connection->dropIndex(
    $table,
    $installer->getIdxName(
        'webapi_user',
        ['user_name'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    )
);

$connection->addColumn(
    $table,
    'company_name',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'Company Name'
    ]
);
$connection->addColumn(
    $table,
    'contact_email',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'Contact Email'
    ]
);
$connection->changeColumn(
    $table,
    'user_name',
    'api_key',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'Web API key'
    ]
);

$connection->addIndex(
    $table,
    $installer->getIdxName(
        'webapi_user',
        ['api_key'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    'api_key',
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();
