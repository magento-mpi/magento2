<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup */
$installer = $this;
$installer->startSetup();

// Add reset password link token column
$installer->getConnection()->addColumn(
    $installer->getTable('admin_user'),
    'rp_token',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        'length' => 256,
        'nullable' => true,
        'default' => null,
        'comment' => 'Reset Password Link Token'
    )
);

// Add reset password link token creation date column
$installer->getConnection()->addColumn(
    $installer->getTable('admin_user'),
    'rp_token_created_at',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
        'comment' => 'Reset Password Link Token Creation Date'
    )
);

$installer->endSetup();
