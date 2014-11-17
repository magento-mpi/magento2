<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->update(
    $installer->getTable('core_translate'),
    array('crc_string' => new \Zend_Db_Expr('CRC32(' . $connection->quoteIdentifier('string') . ')'))
);
