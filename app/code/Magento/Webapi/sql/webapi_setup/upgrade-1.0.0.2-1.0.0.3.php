<?php
/**
 * Update script for Webapi module.
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
$table = $installer->getTable('webapi_user');

$connection->changeColumn(
    $table,
    'api_secret',
    'secret',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'Secret used for authentication.'
    )
);

$installer->endSetup();
