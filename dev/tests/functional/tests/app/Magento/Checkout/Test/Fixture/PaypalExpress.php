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
 * Class PaypalExpress
 * PayPal Express Method
 * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
 *
 * @ZephyrId MAGETWO-12415
 * @package Magento\Checkout\Test\Fixture
 */
class PaypalExpress extends Checkout
{
    /**
     * Paypal customer buyer
     *
     * @var \Magento\Paypal\Test\Fixture\Customer
     */
    private $paypalCustomer;

    /**
     * Customer telephone number
     *
     * @var string
     */
    private $telephoneNumber;

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
     * Get telephone number for billing/shipping address
     *
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * Prepare data for guest checkout using "Checkout with PayPal" button on product page
     */
    protected function _initData()
    {
        //Configuration
        $coreConfig = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $coreConfig->switchData('free_shipping');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_disabled_all_methods');
        $coreConfig->persist();

        $coreConfig->switchData('paypal_express');
        $coreConfig->persist();

        $coreConfig->switchData('default_tax_config');
        $coreConfig->persist();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple');
        $simple->persist();

        $this->products = array(
            $simple
        );

        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->billingAddress->switchData('address_US_1');

        $this->shippingAddresses = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->shippingAddresses->switchData('address_US_1');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('free_shipping');

        $this->paypalCustomer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->paypalCustomer->switchData('customer_US');

        $this->telephoneNumber = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->telephoneNumber->switchData('address_US_1');
        $this->telephoneNumber->getTelephone();

        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => 10
            )
        );
    }
}
