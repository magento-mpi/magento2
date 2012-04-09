<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Core
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->update($installer->getTable('core_translate'), array(
    'crc_string' => new Zend_Db_Expr('CRC32(' . $connection->quoteIdentifier('string') . ')')
));
