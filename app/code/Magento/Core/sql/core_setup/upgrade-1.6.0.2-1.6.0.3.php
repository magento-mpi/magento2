<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core_translate');

$connection->dropIndex($table, $installer->getIdxName(
    'core_translate',
    array('store_id', 'locale', 'string'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
));

$connection->addColumn($table, 'crc_string', array(
    'type'     => \Magento\DB\Ddl\Table::TYPE_BIGINT,
    'nullable' => false,
    'default'  => crc32(\Magento\Core\Model\Translate::DEFAULT_STRING),
    'comment'  => 'Translation String CRC32 Hash',
));

$connection->addIndex($table, $installer->getIdxName(
    'core_translate',
    array('store_id', 'locale', 'crc_string', 'string'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
), array('store_id', 'locale', 'crc_string', 'string'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE);

$installer->endSetup();
