<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
     * @ZephyrId MAGETWO-12996
     */
    public function testOnepageCheckout()
    {
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->persist();
        $address = $customer->getDefaultBillingAddress();
        $address->persist();
        $checkout = Factory::getFixtureFactory()->getMagentoCheckoutRegisteredPaypalExpress();
        $checkout->persist();
        //Steps
        $this->_addProducts($checkout);
        $this->_magentoCheckoutProcess($checkout);
        $this->_processPaypal($checkout);
        $this->_reviewOrder();
        //Verifying
        $this->_verifyOrder($checkout);
    }

    /**
     * No need to for this step if registered customer
     *
     * @param Checkout $fixture
     */
    protected function _checkoutMethod(Checkout $fixture)
    {
    }
}
