<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$quote = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Sales_Model_Quote');
$quote->load('test01', 'reserved_order_id');

$payment = $quote->getPayment();
$payment->setMethod(Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS);
$payment->setAdditionalInformation(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID, 123);

$quote->collectTotals()->save();
