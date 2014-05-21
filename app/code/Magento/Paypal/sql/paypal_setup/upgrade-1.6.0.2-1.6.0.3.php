<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Framework\Module\Setup */
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();
$data = array(
    array('paypal_reversed', 'PayPal Reversed'),
    array('paypal_canceled_reversal', 'PayPal Canceled Reversal')
);
$connection = $installer->getConnection()->insertArray(
    $installer->getTable('sales_order_status'),
    array('status', 'label'),
    $data
);
$installer->endSetup();
