<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$connection->addColumn(
    $installer->getTable('admin_user'),
    'interface_locale',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 5,
        'nullable' => false,
        'default' => 'en_US',
        'comment' => 'Backend interface locale'
    ]
);
$installer->endSetup();
