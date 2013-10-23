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
 * Class GuestAuthorizenet
 * Credit Card (Authorize.net)
 * Guest checkout with Authorize.Net payment method and offline shipping method
 *
 * @ZephyrId MAGETWO-12832
 * @package Magento\Checkout\Test\Fixture
 */
class GuestAuthorizenet extends Checkout
{
    /**
     * Prepare Authorize.Net data
     */
    protected function _initData()
    {
        //Configuration
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData('flat_rate');
        $configFixture->persist();
        $configFixture->switchData('authorizenet');
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
        $this->paymentMethod->switchData('authorizenet');
        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_authorizenet');
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '30'
            )
        );
    }
}
