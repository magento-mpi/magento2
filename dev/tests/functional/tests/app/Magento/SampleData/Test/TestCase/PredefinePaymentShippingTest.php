<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SampleData\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Core\Test\Fixture\ConfigData;

/**
 * Class PredefinePaymentShippingTest
 * Predefine payment and shipping data
 *
 * @ticketId MTA-404
 */
class PredefinePaymentShippingTest extends Injectable
{
    /**
     * Predefine payment and shipping data
     *
     * @param FixtureFactory $fixtureFactory
     * @param string $shippings
     * @param ConfigData $configCurrency
     * @param string $currency
     * @param string $payments
     * @param string $shippingOrigin
     * @return void
     */
    public function test(
        FixtureFactory $fixtureFactory,
        $shippings,
        ConfigData $configCurrency,
        $currency,
        $payments,
        $shippingOrigin
    ) {
        $configCurrency->persist();
        $currencyRate = $fixtureFactory->create('\Magento\CurrencySymbol\Test\Fixture\CurrencyRate');
        $currencyRate->switchData($currency);
        $currencyRate->persist();

        $shippingData = explode(', ', $shippings);
        $shippingData[] = $shippingOrigin;

        foreach ($shippingData as $value) {
            $configFixture = $fixtureFactory->createByCode('configData', ['dataSet' => $value]);
            $configFixture->persist();
        }

        $paymentData = explode(', ', $payments);
        foreach ($paymentData as $value) {
            $configFixture = $fixtureFactory->create('\Magento\Core\Test\Fixture\Config');
            $configFixture->switchData($value);
            $configFixture->persist();
        }
    }
}
