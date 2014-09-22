<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture;

/**
 * Guest checkout with taxes, Check/Money order payment method and offline shipping method
 */
class CheckMoneyOrder extends Checkout
{
    /**
     * Prepare Check/Money order data
     */
    protected function _initData()
    {
        //Verification data
        $this->_data = [
            'totals' => [
                'grand_total' => '141.81',
                'sub_total' => '131.00',
                'tax' => '10.81',
            ],
            'product_price_with_tax' => [
                'SimpleProduct' => [
                    'value' => '10.00',
                ],
                'ConfigurableProduct' => [
                    'value' => '11.00',
                ],
                'BundleFixed' => [
                    'value' => '110.00',
                ],
            ],
        ];
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration([
            'free_shipping',
            'check_money_order',
            'display_price',
            'display_shopping_cart',
            'default_tax_config',
        ]);

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $objectManager = Factory::getObjectManager();
        $taxRule = $objectManager->create('\Magento\Tax\Test\Fixture\TaxRule', ['dataSet' => 'custom_rule']);
        $taxRule->persist();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData('simple_required');
        $simple->persist();
        $configurable = Factory::getFixtureFactory()->getMagentoConfigurableProductConfigurableProduct();
        $configurable->switchData('configurable_required');
        $configurable->persist();
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundleFixed();
        $bundle->switchData('bundle_required');
        $bundle->persist();

        $this->products = [
            $simple,
            $configurable,
            $bundle,
        ];

        //Checkout data
        $this->billingAddress = $objectManager->create(
            '\Magento\Customer\Test\Fixture\AddressInjectable',
            ['dataSet' => 'address_US_1']
        );

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('free_shipping');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('check_money_order');
    }

    /**
     * Get Product Price with tax for product of particular class
     *
     * @param Fixture\SimpleProduct $product
     * @return string
     */
    public function getProductPriceWithTax($product)
    {
        $className = explode('\\', get_class($product));
        return $this->getData('product_price_with_tax/' . $className[count($className) - 1] . '/value');
    }

    /**
     * Get order subtotal
     *
     * @return string
     */
    public function getSubtotal()
    {
        return $this->getData('totals/sub_total');
    }

    /**
     * Get order tax
     *
     * @return string
     */
    public function getTax()
    {
        return $this->getData('totals/tax');
    }
}
