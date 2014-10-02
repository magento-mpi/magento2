<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\ObjectManager;
use Mtf\TestCase\Scenario;

/**
 * Test Creation for Checkout with Gift Messages
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Gift Messages (Order/Items level)
 * 2. Create Product according dataSet
 *
 * Steps:
 * 1. Login as registered customer
 * 2. Add product to Cart and start checkout
 * 3. On Shipping Method section Click "Add gift option"
 * 4. Complete Checkout steps
 * 5. Perform all asserts
 *
 * @group Gift_Messages_(CS)
 * @ZephyrId MAGETWO-28978
 */
class CheckoutWithGiftMessagesTest extends Scenario
{
    /**
     * Steps for scenario
     *
     * @var array
     */
    protected $scenario = [
        'CheckoutWithGiftMessagesTest' => [
            'methods' => [
                'test' => [
                    'scenario' => [
                        'setupConfiguration' => [
                            'module' => 'Magento_Core',
                            'next' => 'createProducts',
                            'arguments' => [
                                'configData' => 'cashondelivery, enableGiftMessages',
                            ],
                        ],
                        'createProducts' => [
                            'module' => 'Magento_Catalog',
                            'next' => 'createCustomer',
                        ],
                        'createCustomer' => [
                            'module' => 'Magento_Customer',
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
                            'next' => 'addGiftMessage',
                        ],
                        'addGiftMessage' => [
                            'module' => 'Magento_GiftMessage',
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
     * Preparing configuration for test
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __prepare(
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Runs one page checkout test
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario($this->scenario);
    }

    /**
     * Logout customer
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
