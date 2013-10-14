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
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('free_shipping')->persist();
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('paypal_disabled_all_methods')->persist();
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('paypal_express')->persist();
        Factory::getFixtureFactory()->getMagentoCoreConfig()->switchData('default_tax_config')->persist();
        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct()->switchData('simple');
        $simple->persist();
        $this->products = array(
            $simple
        );
        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoPaypalCustomer()->switchData('address_US_1');
        $this->shippingAddresses = Factory::getFixtureFactory()->getMagentoPaypalCustomer()->switchData('address_US_1');
        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod()->switchData('free_shipping');
        $this->paypalCustomer = Factory::getFixtureFactory()->getMagentoPaypalCustomer()->switchData('customer_US');
        $this->telephoneNumber = Factory::getFixtureFactory()->getMagentoCustomerAddress()->switchData('address_US_1')
            ->getTelephone();
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => 10
            )
        );
    }
}
