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

$oauthTokenTable = $installer->getTable('oauth_token');
$adminTable = $installer->getTable('admin_user');
$customerTable = $installer->getTable('customer_entity');

$connection->addColumn(
    $oauthTokenTable,
    'user_type',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'comment' => 'User type'
    )
);
$connection->modifyColumn(
    $oauthTokenTable,
    'consumer_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => true,
        'comment' => 'Oauth Consumer ID'
    )
);
$connection->addForeignKey(
    $installer->getFkName($oauthTokenTable, 'admin_id', $adminTable, 'user_id'),
    $oauthTokenTable,
    'admin_id',
    $adminTable,
    'user_id'
);
$connection->addForeignKey(
    $installer->getFkName($oauthTokenTable, 'admin_id', $customerTable, 'entity_id'),
    $oauthTokenTable,
    'customer_id',
    $customerTable,
    'entity_id'
);
