<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\TestCase;

use Mtf\TestCase\Scenario;

/**
 * Test Flow:
 * 1. Precondition:
 * 2. Shipping method is configured (UPS, USPS, Fedex, DHL)
 *
 * 1. Steps:
 * 2. Go to Frontend
 * 3. Add Products to the cart
 * 4. Click the 'Proceed to Checkout' button
 * 5. Select checkout method according to dataset
 * 6. Fill billing information according to "customer/dataSet"
 * 7. Select the 'Ship to this address' option button,  click the 'Continue' button
 * 8. Select shipping method according to shipping/shipping_method, click the 'Continue' button
 * 9. Select payment method according to dataset, click the 'Continue' button
 * 10. Click the 'Place Order' button
 * 11. Perform assertions
 *
 * @group DHL_(CS), FedEx_(CS), One_Page_Checkout_(CS), UPS_(CS), USPS_(CS)
 * @ZephyrId MAGETWO-29901
 */
class OnePageCheckoutWithOnlineShippingMethodsTest extends Scenario
{
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
    }
}
