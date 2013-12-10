<?php
/**
 * Upgrade script for integration table.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var \Magento\Integration\Model\Resource\Setup $installer */
$installer = $this;
$tableName = $installer->getTable('integration');
$installer->getConnection()->addColumn(
    $tableName,
    'setup_type',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
        'unsigned' => true,
        'nullable' => false,
        'default' => 0,
        'comment' => 'Integration type - manual or config file'
    )
);
$installer->getConnection()->addColumn(
    $tableName,
    'identity_link_url',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Identity linking Url'
    )
);