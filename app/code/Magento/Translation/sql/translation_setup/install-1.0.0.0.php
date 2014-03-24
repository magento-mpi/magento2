<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'translation'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('translation'))
    ->addColumn(
        'key_id',
        \Magento\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ),
        'Key Id of Translation'
    )->addColumn(
        'string',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false,
            'default' => \Magento\TranslateInterface::DEFAULT_STRING,
        ),
        'Translation String'
    )->addColumn(
        'store_id',
        \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => '0',
        ),
        'Store Id'
    )->addColumn(
        'translate',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        255,
        array(),
        'Translate'
    )->addColumn(
        'locale',
        \Magento\DB\Ddl\Table::TYPE_TEXT,
        20,
        array(
            'nullable' => false,
            'default'  => 'en_US',
        ),
        'Locale'
    )->addColumn(
        'crc_string',
        \Magento\DB\Ddl\Table::TYPE_BIGINT,
        null,
        array(
            'nullable' => false,
            'default'  => crc32(\Magento\TranslateInterface::DEFAULT_STRING)
        ),
        'Translation String CRC32 Hash'
    )->addIndex(
        $installer->getIdxName(
            'translation',
            array('store_id', 'locale', 'crc_string', 'string'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('store_id', 'locale', 'crc_string', 'string'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE)
    )->addIndex(
        $installer->getIdxName('translation', array('store_id')),
        array('store_id')
    )->addForeignKey(
        $installer->getFkName('translation', 'store_id', 'core_store', 'store_id'),
        'store_id',
        $installer->getTable('core_store'),
        'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\DB\Ddl\Table::ACTION_CASCADE
    )->setComment('Translations');
$installer->getConnection()->createTable($table);

$installer->endSetup();
