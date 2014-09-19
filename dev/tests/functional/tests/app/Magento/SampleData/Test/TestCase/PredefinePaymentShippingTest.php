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
     * @param string $payments
     * @param string $shippingOrigin
     * @return void
     */
    public function test(FixtureFactory $fixtureFactory, $shippings, $payments, $shippingOrigin)
    {
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
