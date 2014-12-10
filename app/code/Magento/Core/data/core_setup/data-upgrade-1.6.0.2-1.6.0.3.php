<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->update(
    $installer->getTable('core_translate'),
    ['crc_string' => new \Zend_Db_Expr('CRC32(' . $connection->quoteIdentifier('string') . ')')]
);
