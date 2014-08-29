<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class GuestPaypalDirect
 * PayPal Payments Pro Method
 * Register on checkout to checkout with multi shipping using PayPal Payments Pro payment method
 *
 */
class GuestPaypalDirect extends Checkout
{
    /**
     * Mapping between products and shipping addresses for multishipping
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * Data for guest multishipping checkout with Payments Pro Method
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => array(
                    '15.83', //simple
                    '16.92' //configurable
                )
            )
        );
    }

    /**
     * Get bindings for multishipping
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
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
            'paypal_direct',
            'display_price',
            'display_shopping_cart',
            'default_tax_config'
        ));
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

        $this->products = array(
            $simple,
            $configurable
        );
        //Checkout data
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customer->switchData('customer_US_1');
        $address1 = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $address1->switchData('address_US_1');
        $address2 = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $address2->switchData('address_US_2');
        $this->shippingAddresses = array(
            $address1,
            $address2
        );

        $newShippingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $newShippingAddress->switchData('address_US_2');
        $this->newShippingAddresses = array($newShippingAddress);

        $shippingMethod1 = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethod1->switchData('flat_rate');
        $shippingMethod2 = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethod2->switchData('flat_rate');
        $this->shippingMethods = array(
            $shippingMethod1,
            $shippingMethod2
        );
        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_direct');
        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_direct');
        $this->bindings = array(
            $simple->getName() => $address1->getOneLineAddress(),
            $configurable->getName() => $address2->getOneLineAddress()
        );
    }
}
