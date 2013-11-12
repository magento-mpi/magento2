<?php
/**
 * Update script for Webapi module to add api related fields for Integration.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var \Magento\Core\Model\Resource\Setup $installer */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$integrationTable = $installer->getTable('integration');
$connection->addColumn(
    $integrationTable,
    'api_permissions',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => '64k',
        'nullable' => true,
        'comment' => 'API resource permissions as csv for the integration'
    )
);
$connection->addColumn(
    $integrationTable,
    'is_api_enabled',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'nullable'  => false,
        'length' => 255,
        'default'   => 'N',
        'comment' => 'API access enabled or disabled',
    )
);
$installer->endSetup();
