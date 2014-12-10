<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

$nodeTableName = $installer->getTable('magento_versionscms_hierarchy_node');

$installer->getConnection()->addColumn(
    $nodeTableName,
    'scope',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => '8',
        'comment' => 'Scope: default|website|store',
        'nullable' => false,
        'default' => 'default'
    ]
);
$installer->getConnection()->addColumn(
    $nodeTableName,
    'scope_id',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'comment' => 'Scope Id',
        'nullable' => false,
        'default' => '0',
        'UNSIGNED' => true
    ]
);

$installer->endSetup();
