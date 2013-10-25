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
     * Prepare Authorize.Net data
     */
    protected function _initData()
    {
        $coreConfig = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $coreConfig->switchData('paypal_disabled_all_methods');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_payflow_pro');
        $coreConfig->persist();

        $coreConfig->switchData('flat_rate');
        $coreConfig->persist();

        $coreConfig->switchData('default_tax_config');
        $coreConfig->persist();

        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();

        $simpleProduct = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct->switchData('simple');
        $simpleProduct->persist();

        $configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProduct->switchData('configurable2');
        $configurableProduct->persist();

        $bundleProduct = Factory::getFixtureFactory()->getMagentoBundleBundle();
        $bundleProduct->switchData('bundle_option_price');
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

        $this->_data = array(
            'totals' => array(
                'grand_total' => '155.90'
            )
        );
    }
}
