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
        $coreConfig = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $coreConfig->switchData('flat_rate');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_disabled_all_methods');
        $coreConfig->persist();

        $coreConfig->switchData('authorizenet_disable');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_direct');
        $coreConfig->persist();

        $coreConfig->switchData('default_tax_config');
        $coreConfig->persist();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple');
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundle();
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();

        $simple->persist();
        $bundle->persist();
        $configurable->persist();
        $this->products = array(
            $simple,
            $bundle,
            $configurable
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
                'grand_total' => '166.72'
            )
        );
    }
}
