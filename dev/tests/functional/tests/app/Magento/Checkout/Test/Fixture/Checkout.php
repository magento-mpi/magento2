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

use Mtf\Fixture\DataFixture;

/**
 * Class Checkout
 *
 * @package Magento\Checkout\Test\Fixture
 */
class Checkout extends DataFixture
{
    /**
     * Customer
     *
     * @var \Magento\Customer\Test\Fixture\Customer
     */
    protected $customer;

    /**
     * Products for checkout
     *
     * @var \Magento\Catalog\Test\Fixture\Product[]
     */
    protected $products = array();

    /**
     * Checkout billing address
     *
     * @var \Magento\Customer\Test\Fixture\Address
     */
    protected $billingAddress;

    /**
     * Checkout shipping addresses
     *
     * @var \Magento\Customer\Test\Fixture\Address[]
     */
    protected $shippingAddresses = array();

    /**
     * Shipping addresses that should be added during checkout
     *
     * @var \Magento\Customer\Test\Fixture\Address[]
     */
    protected $newShippingAddresses = array();

    /**
     * Shipping methods
     *
     * @var \Magento\Shipping\Test\Fixture\Method
     */
    protected $shippingMethods;

    /**
     * Payment method
     *
     * @var \Magento\Payment\Test\Fixture\Method
     */
    protected $paymentMethod;

    /**
     * Credit card which is used for checkout
     *
     * @var \Magento\Payment\Test\Fixture\Cc
     */
    protected $creditCard;

    /**
     * Mapping between products and shipping addresses for multishipping
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
    }

    /**
     * Get product which should be added to shopping cart
     *
     * @return \Magento\Catalog\Test\Fixture\Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get customer type to define how to perform checkout
     *
     * @return \Magento\Customer\Test\Fixture\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Get customer billing address
     *
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Get customer shipping address
     *
     * @return \Magento\Customer\Test\Fixture\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddresses;
    }

    /**
     * Get new shipping addresses
     *
     * @return \Magento\Customer\Test\Fixture\Address[]
     */
    public function getNewShippingAddresses()
    {
        return $this->newShippingAddresses;
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
     * Get shipping methods data
     *
     * @return \Magento\Shipping\Test\Fixture\Method[]
     */
    public function getShippingMethods()
    {
        return $this->shippingMethods;
    }

    /**
     * Get payment method data
     *
     * @return \Magento\Payment\Test\Fixture\Method
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Get credit card
     *
     * @return \Magento\Payment\Test\Fixture\Cc
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }

    /**
     * Get order grand total amount
     *
     * @return string
     */
    public function getGrandTotal()
    {
        return $this->getData('totals/grand_total');
    }
}
