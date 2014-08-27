<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Framework\Module\Setup */

$installer->startSetup();

/**
 * Create table 'drop '
 */
$installer->getConnection()->dropColumn($installer->getTable('log_visitor'), 'session_id');
$installer->getConnection()->addForeignKey(
    $installer->getFkName('log_visitor', 'visitor_id', 'customer_visitor', 'visitor_id'),
    $installer->getTable('log_visitor'),
    'visitor_id',
    $installer->getTable('customer_visitor'),
    'visitor_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
);

$installer->endSetup();
