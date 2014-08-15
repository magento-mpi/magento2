<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Scenario;

/**
 * Class OnePageCheckoutTest
 * OnePageCheckout Test
 */
class OnePageCheckoutTest extends Scenario
{
    /**
     * Steps for scenario
     *
     * @var array
     */
    protected $scenario = [
        'OnePageCheckoutTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'createRewardExchangeRates' => [
                            'module' => 'Magento_Reward',
                            'arguments' => [
                                'rewardRates' => ['rate_points_to_currency', 'rate_currency_to_points'],
                            ],
                            'next' => 'createProducts'
                        ],
                        'createProducts' => [
                            'module' => 'Magento_Catalog',
                            'next' => 'createCustomer'
                        ],
                        'createCustomer' => [
                            'module' => 'Magento_Customer',
                            'next' => 'applyRewardPointsToCustomer'
                        ],
                        'applyRewardPointsToCustomer' => [
                            'module' => 'Magento_Reward',
                            'next' => 'applyCustomerBalanceToCustomer'
                        ],
                        'applyCustomerBalanceToCustomer' => [
                            'module' => 'Magento_CustomerBalance',
                            'next' => 'createGiftCardAccount'
                        ],
                        'createGiftCardAccount' => [
                            'module' => 'Magento_GiftCardAccount',
                            'next' => 'createSalesRule'
                        ],
                        'createSalesRule' => [
                            'module' => 'Magento_SalesRule',
                            'next' => 'goToFrontEnd'
                        ],
                        'goToFrontEnd' => [
                            'module' => 'Magento_Cms',
                            'next' => 'addProductsToTheCart'
                        ],
                        'addProductsToTheCart' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'applyGiftCard',
                        ],
                        'applyGiftCard' => [
                            'module' => 'Magento_GiftCardAccount',
                            'next' => 'applySalesRule',
                        ],
                        'applySalesRule' => [
                            'module' => 'Magento_SalesRule',
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
                            'next' => 'selectRewardPoints',
                        ],
                        'selectRewardPoints' => [
                            'module' => 'Magento_Reward',
                            'next' => 'selectStoreCredit',
                        ],
                        'selectStoreCredit' => [
                            'module' => 'Magento_CustomerBalance',
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
     * @param string $config
     * @return void
     */
    public function test($config)
    {
        $this->configuration = $config;
        $this->setupConfiguration();
        $this->executeScenario($this->scenario);
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
        foreach ($dataSets as $dataSet) {
            $dataSet = trim($dataSet) . $prefix;
            $configuration = $this->fixtureFactory->createByCode('configData', ['dataSet' => $dataSet]);
            $configuration->persist();
        }
    }
}
