<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\TestCase;

use Mtf\TestCase\Scenario;
use Magento\Customer\Test\Page\CustomerAccountLogout;

/**
 * Test Creation for OnePageCheckout with online shipping methods
 *
 * Test Flow:
 * 1.*Precondition:*
 * 2. Shipping method is configured (UPS, USPS, Fedex, DHL)
 * 3. Customer is registered according to "customer/dataSet"
 * 4.
 * 5.*Steps:*
 * 6. Go to Frontend
 * 7. Add Products to the cart
 * 8. click the *Proceed to Checkout* button
 * 9. if $checkoutMethod =  login
 * 10. login as customer
 * 11.    elseif $checkoutMethod =  register
 * 12. select the *Register* option button and click the *Continue* button
 * 13. elseif $checkoutMethod =  guest
 * 14. select the *Checkout as Guest* option button and click the *Continue* button
 * 15. fill billing information according to "customer/dataSet"
 * 16. select the *Ship to this address* option button,  click the *Continue* button
 * 17. select shipping method according to shipping/shipping_method, click the *Continue* button
 * 18. select payment method according to dataset, click the *Continue* button
 * 19. Call assertOrderTotal
 * 20. click the *Place Order* button
 * 21. Perform assertions
 *
 * @group DHL_(CS), FedEx_(CS), One_Page_Checkout_(CS), UPS_(CS), USPS_(CS)
 * @ZephyrId MTA-522
 */
class OnePageCheckoutWithOnlineShippingMethodsTest extends Scenario
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
     * Disable enabled config after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();
        $this->customerAccountLogout->open();
    }
}
