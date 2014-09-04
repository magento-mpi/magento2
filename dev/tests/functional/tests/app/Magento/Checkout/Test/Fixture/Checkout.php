<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Fixture;

use Magento\Customer\Test\Fixture\Address;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Shipping\Test\Fixture\Method;
use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

/**
 * Class Checkout
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
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
     * @var \Magento\Catalog\Test\Fixture\SimpleProduct[]
     */
    protected $products = [];

    /**
     * Checkout billing address
     *
     * @var \Magento\Customer\Test\Fixture\AddressInjectable
     */
    protected $billingAddress;

    /**
     * Checkout shipping addresses
     *
     * @var \Magento\Customer\Test\Fixture\Address[]
     */
    protected $shippingAddresses = [];

    /**
     * Shipping addresses that should be added during checkout
     *
     * @var \Magento\Customer\Test\Fixture\Address[]
     */
    protected $newShippingAddresses = [];

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
     * {inheritdoc}
     */
    protected function _initData()
    {
    }

    /**
     * Setup a set of configurations
     *
     * @param array $dataSets
     */
    protected function _persistConfiguration(array $dataSets)
    {
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        foreach ($dataSets as $dataSet) {
            $configFixture->switchData($dataSet);
            $configFixture->persist();
        }
    }

    /**
     * Get product which should be added to shopping cart
     *
     * @return \Magento\Catalog\Test\Fixture\SimpleProduct[]
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
     * @return \Magento\Customer\Test\Fixture\AddressInjectable
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

    /**
     * Get comment history string
     *
     * @return string
     */
    public function getCommentHistory()
    {
        return $this->getData('totals/comment_history');
    }

    /**
     * Get is customer registered flag
     *
     * @return bool
     */
    public function isRegisteredCustomer()
    {
        return (bool) $this->getData('customer/is_registered');
    }

    /**
     * Set the billing address for this checkout instance
     *
     * @param Address $billingAddress
     */
    public function setBillingAddress(Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }
    /**
     * Set the customer for this checkout instance
     *
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Set the shipping methods for this checkout instance
     *
     * @param Method $shippingMethods
     */
    public function setShippingMethod(Method $shippingMethods)
    {
        $this->shippingMethods = $shippingMethods;
    }

    public function addProduct($product)
    {
        array_unshift($this->products, $product);
    }
}
