<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Checkout\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Guest checkout using PayPal Payflow Link payment method and offline shipping method
 *
 */
class GuestPayPalPayflowLink extends Checkout
{
    /**
     * Paypal customer buyer
     *
     * @var \Magento\Paypal\Test\Fixture\Customer
     */
    private $paypalCustomer;

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
     * Prepare for PayPal Payflow Link Express Edition
     */
    protected function _initData()
    {
        $this->_data = [
            'totals' => [
                'grand_total' => '156.81',
                'comment_history' => 'Authorized amount of $156.81',
            ],
        ];
    }

    /**
     * Create required data
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration([
            'flat_rate',
            'paypal_disabled_all_methods',
            'paypal_payflow_link',
            'default_tax_config',
            'display_price',
            'display_shopping_cart',
        ]);

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
            ['dataSet' => 'customer_US']
        );

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('flat_rate');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_payflow_link');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_payflow');

        $this->paypalCustomer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->paypalCustomer->switchData('customer_US');
    }
}
