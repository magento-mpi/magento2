<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Mtf\ObjectManager;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Scenario;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;

/**
 * OnePageCheckout within offline Payment Methods
 *
 * Test Flow:
 * Preconditions:
 * 1. Configure shipping method.
 * 2. Configure payment method.
 * 3. Create products.
 * 4. Create and setup customer.
 * 5. Create gift card account according to dataset.
 * 6. Create sales rule according to dataset.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add products to the cart.
 * 3. Apply discounts in shopping cart according to dataset.
 * 4. Click the 'Proceed to Checkout' button.
 * 5. Select checkout method according to dataset.
 * 6. Fill billing information and select the 'Ship to this address' option.
 * 7. Select shipping method.
 * 8. Select payment method (use reward points and store credit if available).
 * 9. Verify order total on review step.
 * 10. Place order.
 * 11. Perform assertions.
 *
 * @group One_Page_Checkout_(CS)
 * @ZephyrId MAGETWO-27485
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
                            'next' => 'addProductsToTheCart'
                        ],
                        'addProductsToTheCart' => [
                            'module' => 'Magento_Checkout',
                            'next' => 'applyGiftCard',
                        ],
                        'applyGiftCard' => [
                            'module' => 'Magento_GiftCardAccount',
                            'next' => 'applySalesRuleOnFrontend',
                        ],
                        'applySalesRuleOnFrontend' => [
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
     * Customer logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Reward rate index page
     *
     * @var RewardRateIndex
     */
    protected $rewardRateIndexPage;

    /**
     * Reward rate new page
     *
     * @var RewardRateNew
     */
    protected $rewardRateNewPage;

    /**
     * Preparing configuration for test
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerAccountLogout $customerAccountLogout
     * @param RewardRateIndex $rewardRateIndexPage
     * @param RewardRateNew $rewardRateNewPage
     * @return void
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        CustomerAccountLogout $customerAccountLogout,
        RewardRateIndex $rewardRateIndexPage,
        RewardRateNew $rewardRateNewPage
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->rewardRateIndexPage = $rewardRateIndexPage;
        $this->rewardRateNewPage = $rewardRateNewPage;
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
        if ($this->configuration !== '-') {
            $this->setupConfiguration();
        }
        $this->executeScenario($this->scenario);
    }

    /**
     * Disable enabled config after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
        if ($this->configuration !== '-') {
            $this->setupConfiguration(true);
        }

        // Deleting exchange rates
        $this->rewardRateIndexPage->open();
        while ($this->rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            $this->rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            $this->rewardRateNewPage->getFormPageActions()->delete();
        }
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
