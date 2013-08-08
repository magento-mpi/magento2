<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core_translate');

$connection->dropIndex($table, $installer->getIdxName(
    'core_translate',
    array('store_id', 'locale', 'string'),
    Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
));

$connection->addColumn($table, 'crc_string', array(
    'type'     => Magento_DB_Ddl_Table::TYPE_BIGINT,
    'nullable' => false,
    'default'  => crc32(Mage_Core_Model_Translate::DEFAULT_STRING),
    'comment'  => 'Translation String CRC32 Hash',
));

$connection->addIndex($table, $installer->getIdxName(
    'core_translate',
    array('store_id', 'locale', 'crc_string', 'string'),
    Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE
), array('store_id', 'locale', 'crc_string', 'string'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE);

$installer->endSetup();
