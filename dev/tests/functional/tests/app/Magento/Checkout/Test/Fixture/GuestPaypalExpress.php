<?php
/**
 * {license_notice}
 *
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
        //Verification data
        $this->_data = [
            'totals' => [
                'grand_total' => '156.81',
                'comment_history'   => 'Authorized amount of $156.81',
            ]
        ];
    }

    /**
     * Setup fixture
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration($this->_getConfigFixtures());

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $objectManager = Factory::getObjectManager();
        $taxRule = $objectManager->create('Magento\Tax\Test\Fixture\TaxRule', ['dataSet' => 'custom_rule']);
        $taxRule->persist();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData('simple_required');
        $simple->persist();
        $configurable = Factory::getFixtureFactory()->getMagentoConfigurableProductConfigurableProduct();
        $configurable->switchData('configurable_required');
        $configurable->persist();
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundleFixed();
        $bundle->switchData('bundle_required');
        $bundle->persist();

        $this->products = [
            $simple,
            $configurable,
            $bundle
        ];

        //Checkout data
        $this->billingAddress = $objectManager->create(
            '\Magento\Customer\Test\Fixture\AddressInjectable',
            ['dataSet' => 'customer_US']
        );

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_express');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_direct');

        $this->paypalCustomer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->paypalCustomer->switchData('customer_US');
    }

    /**
     * Init billing address for checkout
     *
     * @return \Magento\Customer\Test\Fixture\AddressInjectable
     */
    protected function _initBillingAddress()
    {
        return Factory::getFixtureFactory()->getMagentoCustomerAddressInjectable();
    }

    /**
     * Get configuration fixtures
     *
     * @return array
     */
    protected function _getConfigFixtures()
    {
        return [
            'flat_rate',
            'paypal_disabled_all_methods',
            'paypal_express',
            'display_price',
            'display_shopping_cart',
            'default_tax_config'
        ];
    }
}
