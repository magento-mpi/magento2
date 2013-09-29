<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Sales_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database for install
 */
$installer->startSetup();

/**
 * Create table 'paypal_settlement_report'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal_settlement_report'))
    ->addColumn('report_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Report Id')
    ->addColumn('report_date', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Report Date')
    ->addColumn('account_id', Magento_DB_Ddl_Table::TYPE_TEXT, 64, array(
        ), 'Account Id')
    ->addColumn('filename', Magento_DB_Ddl_Table::TYPE_TEXT, 24, array(
        ), 'Filename')
    ->addColumn('last_modified', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Last Modified')
    ->addIndex($installer->getIdxName('paypal_settlement_report', array('report_date', 'account_id'), Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('report_date', 'account_id'), array('type' => Magento_DB_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Paypal Settlement Report Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'paypal_settlement_report_row'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal_settlement_report_row'))
    ->addColumn('row_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Row Id')
    ->addColumn('report_id', Magento_DB_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Report Id')
    ->addColumn('transaction_id', Magento_DB_Ddl_Table::TYPE_TEXT, 19, array(
        ), 'Transaction Id')
    ->addColumn('invoice_id', Magento_DB_Ddl_Table::TYPE_TEXT, 127, array(
        ), 'Invoice Id')
    ->addColumn('paypal_reference_id', Magento_DB_Ddl_Table::TYPE_TEXT, 19, array(
        ), 'Paypal Reference Id')
    ->addColumn('paypal_reference_id_type', Magento_DB_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Paypal Reference Id Type')
    ->addColumn('transaction_event_code', Magento_DB_Ddl_Table::TYPE_TEXT, 5, array(
        ), 'Transaction Event Code')
    ->addColumn('transaction_initiation_date', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Transaction Initiation Date')
    ->addColumn('transaction_completion_date', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Transaction Completion Date')
    ->addColumn('transaction_debit_or_credit', Magento_DB_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => false,
        'default'   => 'CR',
        ), 'Transaction Debit Or Credit')
    ->addColumn('gross_transaction_amount', Magento_DB_Ddl_Table::TYPE_DECIMAL, '20,6', array(
        'nullable'  => false,
        'default'   => '0.000000',
        ), 'Gross Transaction Amount')
    ->addColumn('gross_transaction_currency', Magento_DB_Ddl_Table::TYPE_TEXT, 3, array(
        'default'   => '',
        ), 'Gross Transaction Currency')
    ->addColumn('fee_debit_or_credit', Magento_DB_Ddl_Table::TYPE_TEXT, 2, array(
        ), 'Fee Debit Or Credit')
    ->addColumn('fee_amount', Magento_DB_Ddl_Table::TYPE_DECIMAL, '20,6', array(
        'nullable'  => false,
        'default'   => '0.000000',
        ), 'Fee Amount')
    ->addColumn('fee_currency', Magento_DB_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Fee Currency')
    ->addColumn('custom_field', Magento_DB_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Custom Field')
    ->addColumn('consumer_id', Magento_DB_Ddl_Table::TYPE_TEXT, 127, array(
        ), 'Consumer Id')
    ->addIndex($installer->getIdxName('paypal_settlement_report_row', array('report_id')),
        array('report_id'))
    ->addForeignKey($installer->getFkName('paypal_settlement_report_row', 'report_id', 'paypal_settlement_report', 'report_id'),
        'report_id', $installer->getTable('paypal_settlement_report'), 'report_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Paypal Settlement Report Row Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'paypal_cert'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('paypal_cert'))
    ->addColumn('cert_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Cert Id')
    ->addColumn('website_id', Magento_DB_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Website Id')
    ->addColumn('content', Magento_DB_Ddl_Table::TYPE_TEXT, '64K', array(
        ), 'Content')
    ->addColumn('updated_at', Magento_DB_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Updated At')
    ->addIndex($installer->getIdxName('paypal_cert', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('paypal_cert', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        Magento_DB_Ddl_Table::ACTION_CASCADE, Magento_DB_Ddl_Table::ACTION_CASCADE)
    ->setComment('Paypal Certificate Table');
$installer->getConnection()->createTable($table);

/**
 * Add paypal attributes to the:
 *  - sales/flat_quote_payment_item table
 *  - sales/flat_order table
 */
$installer->addAttribute('quote_payment', 'paypal_payer_id', array());
$installer->addAttribute('quote_payment', 'paypal_payer_status', array());
$installer->addAttribute('quote_payment', 'paypal_correlation_id', array());
$installer->addAttribute('order', 'paypal_ipn_customer_notified', array('type' => 'int', 'visible' => false, 'default' => 0));

$data = array();
$statuses = array(
    'pending_paypal' => __('Pending PayPal')
);
foreach ($statuses as $code => $info) {
    $data[] = array(
        'status' => $code,
        'label'  => $info
    );
}
$installer->getConnection()->insertArray(
    $installer->getTable('sales_order_status'),
    array('status', 'label'),
    $data
);

/**
 * Prepare database after install
 */
$installer->endSetup();

