<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

/**
 * Prepare database for install
 */
$installer->startSetup();
/**
 * Add paypal attributes to the:
 *  - sales/flat_quote_payment_item table
 *  - sales/flat_order table
 */
$installer->addAttribute('quote_payment', 'paypal_payer_id', array());
$installer->addAttribute('quote_payment', 'paypal_payer_status', array());
$installer->addAttribute('quote_payment', 'paypal_correlation_id', array());
$installer->addAttribute(
    'order',
    'paypal_ipn_customer_notified',
    array('type' => 'int', 'visible' => false, 'default' => 0)
);

$data = array();
$statuses = array('pending_paypal' => __('Pending PayPal'));
foreach ($statuses as $code => $info) {
    $data[] = array('status' => $code, 'label' => $info);
}
$installer->getConnection()->insertArray($installer->getTable('sales_order_status'), array('status', 'label'), $data);

/**
 * Prepare database after install
 */
$installer->endSetup();
