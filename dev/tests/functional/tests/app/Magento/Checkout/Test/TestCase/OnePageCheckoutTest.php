<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;

class OnePageCheckoutTest extends Injectable
{
    /**
     * Steps for scenario
     *
     * @var array
     */
    protected $config = [
        'OnePageCheckoutTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'createRewardExchangeRate' => [
                            'module' => 'Magento_Reward',
                            'next' => 'createProducts'
                        ],
                        'createProducts' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'createCustomer'
                        ],
                        'createCustomer' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'createGiftCardAccount'
                        ],
                        'createGiftCardAccount' => [
                            'module' => 'Magento_GiftCardAccount',
                            'next' => 'goToFrontEnd'
                        ],
                        'goToFrontEnd' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'addProductsToTheCart'
                        ],
                        'addProductsToTheCart' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'proceedToCheckout',
                        ],
                        'proceedToCheckout' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'selectCheckoutMethod',
                        ],
                        'selectCheckoutMethod' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'fillBillingInformation',
                        ],
                        'fillBillingInformation' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'fillShippingMethod',
                        ],
                        'fillShippingMethod' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'selectPaymentMethod',
                        ],
                        'selectPaymentMethod' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'placeOrder',
                        ],
                        'placeOrder' => [
                            'module' => 'Magento_Checkout',
                        ],
                    ]
                ]
            ]
        ]
    ];

    /**
     * Factory for Fixtures
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Configuration data set name
     *
     * @var string
     */
    protected $configuration;

    /**
     * Preparing configuration for test
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Runs one page checkout test
     *
     * @param ObjectManager $objectManager
     * @param CustomerInjectable $customer
     * @param array $shipping
     * @param $products
     * @param $checkoutMethod
     * @param $giftCardAccount
     * @param $salesRule
     * @param $grandTotal
     * @param $paymentMethod
     * @param $orderStatus
     * @param $orderButtonsAvailable
     * @param $config
     * @return void
     */
    public function test(
        ObjectManager $objectManager,
        CustomerInjectable $customer,
        array $shipping,
        $products,
        $checkoutMethod,
        $giftCardAccount,
        $salesRule,
        $grandTotal,
        $paymentMethod,
        $orderStatus,
        $orderButtonsAvailable,
        $config
    ) {
        $this->configuration = $config;
        $this->setupConfiguration();
        $this->executeScenario('OnePageCheckoutTest', 'test', $this->config);
    }

    /**
     * Disable enabled config after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->setupConfiguration(true);
    }

    /**
     * Setup configuration
     *
     * @param bool $rollback
     * @return void
     */
    protected function setupConfiguration($rollback = false)
    {
        $prefix = ($rollback == false) ? '' : '_rollback';
        $dataSets = explode(',', $this->configuration);
        foreach ($dataSets as $chunk) {
            $dataSet = trim($chunk) . $prefix;
            $configuration = $this->fixtureFactory->createByCode('configData', ['dataSet' => $dataSet]);
            $configuration->persist();
        }
    }
}
