<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\PaypalExpress;

use Magento\Checkout\Test\Fixture\Checkout;
use Mtf\Factory\Factory;

/**
 * Test registered checkout using PayPal Express Checkout method and offline shipping method.
 */
class CheckoutOnepageTest extends \Magento\Checkout\Test\TestCase\Guest\PaypalExpress\CheckoutOnepageTest
{
    /**
     * Registered checkout using PayPal Express Checkout method and offline shipping method
     *
     * @dataProvider dataProviderPaymentMethod
     * @ZephyrId MAGETWO-12996
     */
    public function testOnepageCheckout(Checkout $fixture)
    {
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->persist();
        $address = $customer->getDefaultBillingAddress();
        $address->persist();

        $fixture->persist();

        //Steps
        $this->_addProducts($fixture);
        $this->_magentoCheckoutProcess($fixture);
        $this->_processPaypal($fixture);

        //Verifying
        $this->_verifyOrder($fixture);
    }

    /**
     * No need to for this step if registered customer
     *
     * @param Checkout $fixture
     */
    protected function _checkoutMethod(Checkout $fixture)
    {
    }

    public function dataProviderPaymentMethod()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoCheckoutRegisteredPaypalExpress()]
        ];
    }
}
