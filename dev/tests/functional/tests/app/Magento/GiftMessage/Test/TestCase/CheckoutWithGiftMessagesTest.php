<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\TestCase;

use Mtf\TestCase\Scenario;
use Magento\Customer\Test\Page\CustomerAccountLogout;

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
        $this->executeScenario();
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
