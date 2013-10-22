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

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class GuestPaypalDirect
 * PayPal Payments Pro Method
 * Guest checkout using PayPal Payments Pro method and offline shipping method
 *
 * @ZephyrId MAGETWO-12968
 * @package Magento\Checkout\Test\Fixture
 */
class GuestPaypalDirect extends Checkout
{
    /**
     * Prepare data for guest checkout with PayPal Payments Pro Method
     */
    protected function _initData()
    {
        //Configuration
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData('flat_rate');
        $configFixture->persist();
        $configFixture->switchData('paypal_disabled_all_methods');
        $configFixture->persist();
        $configFixture->switchData('paypal_direct');
        $configFixture->persist();
        $configFixture->switchData('default_tax_config');
        $configFixture->persist();
        //Products
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple1->switchData('simple');
        $simple2 = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple2->switchData('simple');
        $simple1->persist();
        $simple2->persist();
        $this->products = array(
            $simple1,
            $simple2
        );
        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_US_1');
        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');
        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_direct');
        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_direct');
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => 30
            )
        );
    }
}
