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

$objectManager = Bootstrap::getObjectManager();
// Mock Profile class, because no default implementation of \Magento\Payment\Model\Recurring\Profile\MethodInterface
$profile = \PHPUnit_Framework_MockObject_Generator::getMock(
    'Magento\RecurringProfile\Model\Profile',
    ['isValid'],
    [
        $objectManager->get('Magento\Core\Model\Context'),
        $objectManager->get('Magento\Core\Model\Registry'),
        $objectManager->get('Magento\Payment\Helper\Data'),
        $objectManager->get('Magento\RecurringProfile\Model\PeriodUnits'),
        $objectManager->get('Magento\RecurringProfile\Block\Fields'),
        $objectManager->get('Magento\Core\Model\LocaleInterface'),
        $objectManager->get('Magento\Sales\Model\OrderFactory'),
        $objectManager->get('Magento\Sales\Model\Order\AddressFactory'),
        $objectManager->get('Magento\Sales\Model\Order\PaymentFactory'),
        $objectManager->get('Magento\Sales\Model\Order\ItemFactory'),
        $objectManager->get('Magento\Math\Random'),
        $objectManager->get('Magento\RecurringProfile\Model\States')
    ]
);
$profile->expects(new \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
    ->method('isValid')
    ->will(new \PHPUnit_Framework_MockObject_Stub_Return(true));
/** @var Magento\RecurringProfile\Model\Profile $profile */
$profile
    ->setQuote(Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote')->load(1))
    ->setPeriodUnit('year')
    ->setPeriodFrequency(1)
    ->setScheduleDescription(FIXTURE_RECURRING_PROFILE_SCHEDULE_DESCRIPTION)
    ->setBillingAmount(1)
    ->setCurrencyCode('USD')
    ->setInternalReferenceId('rp-1')
    ->setCustomerId(1)
    ->save();
