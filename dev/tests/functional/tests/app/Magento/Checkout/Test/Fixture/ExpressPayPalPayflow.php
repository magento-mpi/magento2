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
 * Guest checkout with taxes, PayPal Payflow Edition payment method and offline shipping method
 *
 * @package Magento\Checkout\Test\Fixture
 */
class ExpressPayPalPayflow extends Checkout
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
     * Create required data
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration(array(
            'flat_rate',
            'paypal_disabled_all_methods',
            'paypal_payflow_pro',
            'default_tax_config',
            'display_price',
            'display_shopping_cart'
        ));

        Factory::getApp()->magentoTaxRemoveTaxRule();
        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();

        $simpleProduct = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct->switchData('simple');
        $simpleProduct->persist();

        $configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProduct->switchData('configurable_default_category');
        $configurableProduct->persist();

        $bundleProduct = Factory::getFixtureFactory()->getMagentoBundleBundle();
        $bundleProduct->persist();

        $this->products = array(
            $simpleProduct,
            $bundleProduct,
            $configurableProduct
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
    }

    /**
     * Prepare Authorize.Net data
     */
    protected function _initData()
    {
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$156.81'
            )
        );
    }
}
