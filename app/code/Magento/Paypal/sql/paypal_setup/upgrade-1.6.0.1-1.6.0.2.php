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

$installer->getConnection()
    ->addColumn($installer->getTable('paypal_settlement_report_row'), 'payment_tracking_id', array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'comment' => 'Payment Tracking ID',
        'length'  => '255'
    ));
