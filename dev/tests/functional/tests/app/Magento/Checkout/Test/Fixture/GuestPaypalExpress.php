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

/**
 * Class GuestPaypalExpress
 * PayPal Express Method
 * Guest checkout using PayPal Express Checkout method and offline shipping method
 *
 * @ZephyrId MAGETWO-12413
 * @package Magento\Checkout\Test\Fixture
 */
class GuestPaypalExpress extends Checkout
{
    /**
     * Paypal customer buyer
     *
     * @var \Magento\Paypal\Test\Fixture\Customer
     */
    private $paypalCustomer;

    /**
     * Get Paypal buyer account
     *
     * @return \Magento\Paypal\Test\Fixture\Customer
     */
    public function getPaypalCustomer()
    {
        return $this->paypalCustomer;
    }

    /**
     * Prepare data for guest checkout with PayPal Express
     */
    protected function _initData()
    {
        //Configuration
        $coreConfig = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $coreConfig->switchData('flat_rate');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_disabled_all_methods');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_express');
        $coreConfig->persist();

        $coreConfig->switchData('default_tax_config');
        $coreConfig->persist();

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
        $this->paymentMethod->switchData('paypal_express');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_direct');

        $this->paypalCustomer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->paypalCustomer->switchData('customer_US');

        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => 30
            )
        );
    }
}
