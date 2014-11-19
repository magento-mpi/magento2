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
 * Precondition:
 * 1. Shipping method is configured (UPS, USPS, Fedex, DHL)
 *
 * Steps:
 * 1. Go to Frontend
 * 2. Add Products to the cart
 * 3. Click the 'Proceed to Checkout' button
 * 4. Select checkout method according to dataset
 * 5. Fill billing information according to "customer/dataSet"
 * 6. Select the 'Ship to this address' option button,  click the 'Continue' button
 * 7. Select shipping method according to shipping/shipping_method, click the 'Continue' button
 * 8. Select payment method according to dataset, click the 'Continue' button
 * 9. Click the 'Place Order' button
 * 10. Perform assertions
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
