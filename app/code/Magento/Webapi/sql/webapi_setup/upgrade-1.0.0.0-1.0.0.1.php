<?php
/**
 * Update script for Webapi module.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var Magento\Setup\Module\SetupModule $installer */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$table = $installer->getTable('webapi_user');
$connection->addColumn(
    $table,
    'api_secret',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'API Secret used for authentication.'
    )
);

$installer->endSetup();
