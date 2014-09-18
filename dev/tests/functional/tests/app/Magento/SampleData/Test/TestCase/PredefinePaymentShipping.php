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
 * Class PredefinePaymentShipping
 * Predefine payment and shipping data
 *
 * @ticketId MTA-404
 */
class PredefinePaymentShipping extends Injectable
{
    /**
     * Predefine payment and shipping data
     *
     * @param FixtureFactory $fixtureFactory
     * @param string $shipping
     * @param string $payment
     * @param string $shippingOrigin
     * @return void
     */
    public function test(FixtureFactory $fixtureFactory, $shipping, $payment, $shippingOrigin)
    {
        $shippingData = explode(', ', $shipping);
        $shippingData[] = $shippingOrigin;

        foreach ($shippingData as $value) {
            $configFixture = $fixtureFactory->createByCode('configData', ['dataSet' => $value]);
            $configFixture->persist();
        }

        $paymentData = explode(', ', $payment);
        foreach ($paymentData as $value) {
            $configFixture = $fixtureFactory->create('\Magento\Core\Test\Fixture\Config');
            $configFixture->switchData($value);
            $configFixture->persist();
        }
    }
}
