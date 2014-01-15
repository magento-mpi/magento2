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
 * Class PaypalPayflowPro
 * PayPal Payflow Pro Method
 * Guest checkout using PayPal Payments Pro method and offline shipping method
 *
 * @package Magento\Checkout\Test\Fixture
 */
class PaypalPayflowPro extends Checkout
{
    /**
     * Prepare data for guest checkout with PayPal Payflow Pro Method
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }

    /**
     * Setup fixture
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration(array(
            'flat_rate',
            'paypal_disabled_all_methods',
            'paypal_payflow_pro',
            'display_price',
            'display_shopping_cart'
        ));

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData('simple_required');
        $simple->persist();
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable_required');
        $configurable->persist();
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundleFixed();
        $bundle->switchData('bundle_required');
        $bundle->persist();

        $this->products = array(
            $simple,
            $configurable,
            $bundle
        );

        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_US_1');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_payflow_pro');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_default');

        $this->customer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->customer->switchData('customer_US');
    }
}
