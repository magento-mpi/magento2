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
 * Class SpecialPriceCheckMoneyOrder
 * Registered shoppers checkout using check or money order
 *
 * @ZephyrId MAGETWO-12429
 * @package Magento\Checkout\Test\Fixture
 */
class SpecialPriceCheckMoneyOrder extends Checkout
{
    /**
     * Configurable product
     *
     * @var \Magento\Catalog\Test\Fixture\ConfigurableProduct
     */
    protected $configurableProduct;

    /**
     * Simple product
     *
     * @var \Magento\Catalog\Test\Fixture\Product
     */
    protected $simpleProduct;

    /**
     * Return the configurable product
     *
     * @return \Magento\Catalog\Test\Fixture\ConfigurableProduct
     */
    public function getConfigurableProduct()
    {
        return $this->configurableProduct;
    }

    /**
     * Return the simple product
     *
     * @return \Magento\Catalog\Test\Fixture\Product
     */
    public function getSimpleProduct()
    {
        return $this->simpleProduct;
    }

    /**
     * Prepare data for registered customer checkout with check or money order
     */
    protected function _initData()
    {
        // Verification data
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$30.57'
            )
        );
    }

    /**
     * Setup fixture
     */
    public function persist()
    {
        // Configuration
        $this->_persistConfiguration(array(
            'flat_rate',
         ));

        // Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();

        // Products with advanced pricing
        $this->simpleProduct = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $this->simpleProduct->switchData('simple_advanced_pricing');
        $this->simpleProduct->persist();

        $this->configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $this->configurableProduct->switchData('configurable_advanced_pricing');
        $this->configurableProduct->persist();

        $this->products = array(
            $this->simpleProduct,
            $this->configurableProduct
        );

        //Checkout data
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customer->switchData('customer_US_1');

        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_US_1_register');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('check_money_order');

        return $this;
    }
}
