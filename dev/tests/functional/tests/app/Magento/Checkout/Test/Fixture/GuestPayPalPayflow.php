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
class GuestPayPalPayflow extends Checkout
{
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
        $this->billingAddress->switchData('address_US_3');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate_2');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_payflow_pro');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_default');
    }

    /**
     * Prepare Authorize.Net data
     */
    protected function _initData()
    {
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$167.63',
                'authorized_amount' => '$167.63',
                'comment_history' => 'We will authorize $167.63'
            )
        );
    }
}
