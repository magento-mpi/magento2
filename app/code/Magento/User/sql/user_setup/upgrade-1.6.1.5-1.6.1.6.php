<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Module\Setup */
$installer = $this;

$connection = $installer->getConnection();

// Increase length of the password column to accommodate passwords with long salts
$connection->changeColumn(
    $installer->getTable('admin_user'),
    'password',
    'password',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'User Password'
    )
);
