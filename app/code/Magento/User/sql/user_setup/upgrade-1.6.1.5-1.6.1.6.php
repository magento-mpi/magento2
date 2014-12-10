<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$connection = $installer->getConnection();

// Increase length of the password column to accommodate passwords with long salts
$connection->changeColumn(
    $installer->getTable('admin_user'),
    'password',
    'password',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'User Password'
    ]
);
