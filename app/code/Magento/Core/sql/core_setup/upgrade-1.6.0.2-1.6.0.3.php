<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core_translate');

$connection->dropIndex(
    $table,
    $installer->getIdxName(
        'core_translate',
        ['store_id', 'locale', 'string'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    )
);

$connection->addColumn(
    $table,
    'crc_string',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
        'nullable' => false,
        'default' => crc32('Translate String'),
        'comment' => 'Translation String CRC32 Hash'
    ]
);

$connection->addIndex(
    $table,
    $installer->getIdxName(
        'core_translate',
        ['store_id', 'locale', 'crc_string', 'string'],
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    ['store_id', 'locale', 'crc_string', 'string'],
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();
