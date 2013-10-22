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
 * Class MultishippingGuestPaypalDirect
 * PayPal Payments Pro Method
 * Register on checkout to checkout with multi shipping using PayPal Payments Pro payment method
 *
 * @ZephyrId MAGETWO-12836
 * @package Magento\Checkout\Test\Fixture
 */
class MultishippingGuestPaypalDirect extends Checkout
{
    /**
     * Prepare data for guest multishipping checkout with Payments Pro Method
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
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer()->switchData('customer_US_1');
        $address1 = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $address1->switchData('address_US_1');
        $address2 = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $address2->switchData('address_US_2');
        $this->shippingAddresses = array(
            $address1,
            $address2
        );
        $this->newShippingAddresses = array(
            Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_US_2')
        );
        $this->shippingMethods = array(
            Factory::getFixtureFactory()->getMagentoShippingMethod()->switchData('flat_rate'),
            Factory::getFixtureFactory()->getMagentoShippingMethod()->switchData('flat_rate')
        );
        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod()->switchData('paypal_direct');
        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc()->switchData('visa_direct');
        $this->bindings = array(
            $simple1->getProductName() => $address1->getOneLineAddress(),
            $simple2->getProductName() => $address2->getOneLineAddress()
        );
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => 15
            )
        );
    }
}
