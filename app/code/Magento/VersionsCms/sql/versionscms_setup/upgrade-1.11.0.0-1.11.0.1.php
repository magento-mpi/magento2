<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

$nodeTableName = $installer->getTable('magento_versionscms_hierarchy_node');

$installer->getConnection()->addColumn(
    $nodeTableName,
    'scope',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => '8',
        'comment' => 'Scope: default|website|store',
        'nullable' => false,
        'default' => 'default'
    )
);
$installer->getConnection()->addColumn(
    $nodeTableName,
    'scope_id',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'comment' => 'Scope Id',
        'nullable' => false,
        'default' => '0',
        'UNSIGNED' => true
    )
);

$installer->endSetup();
