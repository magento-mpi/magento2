<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('magento_versionscms_hierarchy_metadata'),
    'top_menu_visibility',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'comment' => 'Top Menu Visibility',
        'nullable' => true,
        'default' => null,
        'unsigned' => true
    ]
);

$installer->getConnection()->addColumn(
    $installer->getTable('magento_versionscms_hierarchy_metadata'),
    'top_menu_excluded',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        'comment' => 'Top Menu Excluded',
        'nullable' => true,
        'default' => null,
        'unsigned' => true
    ]
);
