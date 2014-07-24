<?php
/**
 * Upgrade script for integration table.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Integration\Model\Resource\Setup */
$installer = $this;

/* @var $connection \Magento\Framework\DB\Adapter\AdapterInterface */
$connection = $installer->getConnection();

$connection->addColumn(
    $installer->getTable('oauth_token'),
    'user_type',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'comment' => 'User type'
    )
);
$connection->dropColumn(
    $installer->getTable('oauth_token'),
    'admin_id'
);
$connection->dropColumn(
    $installer->getTable('oauth_token'),
    'customer_id'
);
