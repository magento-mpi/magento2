<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

/**
 * Create table 'paypal_payment_transaction'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal_payment_transaction'))
    ->addColumn('transaction_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('txn_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        ), 'Txn Id')
    ->addColumn('additional_information', \Magento\DB\Ddl\Table::TYPE_BLOB, '64K', array(
        ), 'Additional Information')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addIndex(
        $installer->getIdxName(
            'paypal_payment_transaction',
            array('txn_id'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('txn_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->setComment('PayPal Payflow Link Payment Transaction');
$installer->getConnection()->createTable($table);
