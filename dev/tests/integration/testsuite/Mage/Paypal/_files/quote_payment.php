<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$quote = new Mage_Sales_Model_Quote();
$quote->load('test01', 'reserved_order_id');

$payment = $quote->getPayment();
$payment->setMethod(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
$payment->setAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID, 123);

$quote->collectTotals()->save();
