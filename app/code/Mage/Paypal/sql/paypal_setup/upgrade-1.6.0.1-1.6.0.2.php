<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Paypal_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('paypal_settlement_report_row'), 'payment_tracking_id', array(
        'type'    => Magento_DB_Ddl_Table::TYPE_TEXT,
        'comment' => 'Payment Tracking ID',
        'length'  => '255'
    ));