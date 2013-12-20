<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Customer\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();

/**
 * Add unique index for customer_entity table
 */
$connection->addIndex(
    $installer->getTable('customer_entity'),
    $installer->getIdxName('customer_entity', array('email', 'website_id')),
    array('email', 'website_id'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
);