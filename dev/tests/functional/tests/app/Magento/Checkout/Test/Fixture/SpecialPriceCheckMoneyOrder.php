<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class SpecialPriceCheckMoneyOrder
 * Registered shoppers checkout using check or money order
 *
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
     * @var \Magento\Catalog\Test\Fixture\SimpleProduct
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
     * @return \Magento\Catalog\Test\Fixture\SimpleProduct
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
        $this->_data = [
            'totals' => [
                'grand_total' => '$30.57'
            ]
        ];
    }

    /**
     * Setup fixture
     */
    public function persist()
    {
        // Configuration
        $this->_persistConfiguration([
            'flat_rate',
            'enable_mysql_search'
         ]);

        // Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $objectManager = Factory::getObjectManager();
        $taxRule = $objectManager->create('Magento\Tax\Test\Fixture\TaxRule', ['dataSet' => 'custom_rule']);
        $taxRule->persist();

        // Products with advanced pricing
        $this->simpleProduct = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $this->simpleProduct->switchData('simple_advanced_pricing');
        $this->simpleProduct->persist();

        $this->configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $this->configurableProduct->switchData('configurable_advanced_pricing');
        $this->configurableProduct->persist();

        $this->products = [
            $this->simpleProduct,
            $this->configurableProduct
        ];

        //Checkout data
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customer->switchData('customer_US_1');
        $this->customer->persist();

        $this->billingAddress = $objectManager->create(
            '\Magento\Customer\Test\Fixture\AddressInjectable',
            ['dataSet' => 'address_data_US_1']
        );

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('check_money_order');

        return $this;
    }
}
