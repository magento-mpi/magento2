<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Requires Magento/Sales/_files/quote.php
// Requires Magento/Customer/_files/customer.php
use Magento\TestFramework\Helper\Bootstrap;


/** @var Magento\RecurringPayment\Model\Payment $payment */
$payment = Bootstrap::getObjectManager()->create('Magento\RecurringPayment\Model\Payment');
$payment->setQuote(
    Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote')->load(1)
)->setPeriodUnit(
    'year'
)->setPeriodFrequency(
    1
)->setScheduleDescription(
    'Test Schedule'
)->setBillingAmount(
    1
)->setCurrencyCode(
    'USD'
)->setMethodCode(
    'paypal_express'
)->setInternalReferenceId(
    'rp-1'
)->setCustomerId(
    1
)->save();
