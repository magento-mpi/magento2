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

define('FIXTURE_RECURRING_PROFILE_SCHEDULE_DESCRIPTION', 'fixture-recurring-profile-schedule');

/** @var Magento\RecurringProfile\Model\Profile $profile */
$profile = Bootstrap::getObjectManager()->create('Magento\RecurringProfile\Model\Profile');
$profile
    ->setQuote(Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote')->load(1))
    ->setPeriodUnit('year')
    ->setPeriodFrequency(1)
    ->setScheduleDescription(FIXTURE_RECURRING_PROFILE_SCHEDULE_DESCRIPTION)
    ->setBillingAmount(1)
    ->setCurrencyCode('USD')
    ->setMethodCode('paypal_express')
    ->setInternalReferenceId('rp-1')
    ->setCustomerId(1)
    ->save();
