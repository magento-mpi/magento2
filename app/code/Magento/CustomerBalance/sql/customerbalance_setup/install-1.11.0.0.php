<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer Magento_CustomerBalance_Model_Resource_Setup */
$installer->startSetup();

/**
 * Create table 'magento_customerbalance'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_customerbalance'))
    ->addColumn('balance_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Balance Id')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Website Id')
    ->addColumn('amount', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Balance Amount')
    ->addColumn('base_currency_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 3, array(
        ), 'Base Currency Code')
    ->addIndex($installer->getIdxName('magento_customerbalance', array('customer_id', 'website_id'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('customer_id', 'website_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magento_customerbalance', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('magento_customerbalance', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magento_customerbalance', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Customerbalance');
$installer->getConnection()->createTable($table);

/**
 * Create table 'magento_customerbalance_history'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magento_customerbalance_history'))
    ->addColumn('history_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'History Id')
    ->addColumn('balance_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Balance Id')
    ->addColumn('updated_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')
    ->addColumn('action', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Action')
    ->addColumn('balance_amount', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Balance Amount')
    ->addColumn('balance_delta', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Balance Delta')
    ->addColumn('additional_info', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Additional Info')
    ->addColumn('is_customer_notified', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Customer Notified')
    ->addIndex($installer->getIdxName('magento_customerbalance_history', array('balance_id')),
        array('balance_id'))
    ->addForeignKey($installer->getFkName('magento_customerbalance_history', 'balance_id', 'magento_customerbalance', 'balance_id'),
        'balance_id', $installer->getTable('magento_customerbalance'), 'balance_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Enterprise Customerbalance History');
$installer->getConnection()->createTable($table);

$installer->endSetup();
// Modify Sales Entities
//  0.0.5 => 0.0.6
// Renamed: base_customer_balance_amount_used => base_customer_bal_amount_used
$installer->addAttribute('quote', 'customer_balance_amount_used', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_customer_bal_amount_used', array('type'=>'decimal'));


$installer->addAttribute('quote_address', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'customer_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'base_customer_balance_invoiced', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_balance_invoiced', array('type'=>'decimal'));

$installer->addAttribute('order', 'base_customer_balance_refunded', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_balance_refunded', array('type'=>'decimal'));

$installer->addAttribute('invoice', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'customer_balance_amount', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'base_customer_balance_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'customer_balance_amount', array('type'=>'decimal'));

// 0.0.6 => 0.0.7
$installer->addAttribute('quote', 'use_customer_balance', array('type'=>'integer'));

// 0.0.9 => 0.0.10
// Renamed: base_customer_balance_total_refunded    => bs_customer_bal_total_refunded
// Renamed: length: customer_balance_total_refunded => customer_bal_total_refunded
$installer->addAttribute('creditmemo', 'bs_customer_bal_total_refunded', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'customer_bal_total_refunded', array('type'=>'decimal'));

$installer->addAttribute('order', 'bs_customer_bal_total_refunded', array('type'=>'decimal'));
$installer->addAttribute('order', 'customer_bal_total_refunded', array('type'=>'decimal'));
