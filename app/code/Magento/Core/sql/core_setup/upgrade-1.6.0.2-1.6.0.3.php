<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core_translate');

$connection->dropIndex(
    $table,
    $installer->getIdxName(
        'core_translate',
        array('store_id', 'locale', 'string'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    )
);

$connection->addColumn(
    $table,
    'crc_string',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
        'nullable' => false,
        'default' => crc32('Translate String'),
        'comment' => 'Translation String CRC32 Hash'
    )
);

$connection->addIndex(
    $table,
    $installer->getIdxName(
        'core_translate',
        array('store_id', 'locale', 'crc_string', 'string'),
        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
    ),
    array('store_id', 'locale', 'crc_string', 'string'),
    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();
