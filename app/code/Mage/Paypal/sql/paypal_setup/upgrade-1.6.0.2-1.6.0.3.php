<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Core_Model_Resource_Setup */
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
