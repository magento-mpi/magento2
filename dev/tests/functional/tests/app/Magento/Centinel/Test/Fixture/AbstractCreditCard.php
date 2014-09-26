<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Guest checkout. 3D Secure payment method.
 *
 */
abstract class AbstractCreditCard extends Checkout
{
    /**
     * Create required data
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration($this->getConfiguration());

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData($this->getProductTypeName());
        $simple->persist();

        $this->products = [
            $simple
        ];

        //Customer
        if ($this->getCustomerName()) {
            $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
            $this->customer->switchData($this->getCustomerName());
        }

        //Checkout data
        if ($this->getBillingAddressName()) {
            $objectManager = Factory::getObjectManager();
            $this->billingAddress = $objectManager->create(
                '\Magento\Customer\Test\Fixture\AddressInjectable',
                ['dataSet' => $this->getBillingAddressName()]
            );
        }

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData($this->getShippingMethodName());

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData($this->getPaymentMethodName());

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData($this->getCreditCardName());
    }

    /**
     * Get Payment Information - 3D Secure Verification Result
     *
     * @return string
     */
    public function getVerificationResult()
    {
        return $this->getData('payment_info/verification_result');
    }

    /**
     * Get Payment Information - 3D Secure Cardholder Validation
     *
     * @return string
     */
    public function getCardholderValidation()
    {
        return $this->getData('payment_info/cardholder_validation');
    }

    /**
     * Get Payment Information - 3D Secure Electronic Commerce Indicator
     *
     * @return string
     */
    public function getEcommerceIndicator()
    {
        return $this->getData('payment_info/electronic_commerce_indicator');
    }

    /**
     * Get Checkout Data - Billing Address
     *
     * @return string
     */
    public function getBillingAddressName()
    {
        return $this->getData('checkout_data/billing_address');
    }

    /**
     * Get Checkout Data - Billing Address
     *
     * @return string
     */
    public function getShippingMethodName()
    {
        return $this->getData('checkout_data/shipping_methods');
    }

    /**
     * Get Checkout Data - Billing Address
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        return $this->getData('checkout_data/payment_method');
    }

    /**
     * Get Checkout Data - Billing Address
     *
     * @return string
     */
    public function getCreditCardName()
    {
        return $this->getData('checkout_data/credit_card');
    }

    /**
     * Get Configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->getData('configuration');
    }

    /**
     * Get Product Type
     *
     * @return string
     */
    public function getProductTypeName()
    {
        return $this->getData('product_type');
    }

    /**
     * Get Customer
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getData('customer/name');
    }
}
