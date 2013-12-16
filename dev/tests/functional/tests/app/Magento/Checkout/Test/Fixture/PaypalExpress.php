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
     * Get telephone number
     *
     * @return array
     */
    public function getTelephoneNumber()
    {
        return array('telephone' => $this->telephoneNumber);
    }

    /**
     * Prepare data for guest checkout using "Checkout with PayPal" button on product page
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$10.83'
            )
        );
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration(array(
            'free_shipping',
            'paypal_disabled_all_methods',
            'paypal_express',
            'default_tax_config',
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

        $customerAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $customerAddress->switchData('address_US_1');
        $this->telephoneNumber = $customerAddress->getTelephone();
    }
}
