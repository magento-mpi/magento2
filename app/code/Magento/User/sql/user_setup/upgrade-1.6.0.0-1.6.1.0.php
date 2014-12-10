<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

// Add reset password link token column
$installer->getConnection()->addColumn(
    $installer->getTable('admin_user'),
    'rp_token',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 256,
        'nullable' => true,
        'default' => null,
        'comment' => 'Reset Password Link Token'
    ]
);

// Add reset password link token creation date column
$installer->getConnection()->addColumn(
    $installer->getTable('admin_user'),
    'rp_token_created_at',
    [
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
        'comment' => 'Reset Password Link Token Creation Date'
    ]
);

$installer->endSetup();
