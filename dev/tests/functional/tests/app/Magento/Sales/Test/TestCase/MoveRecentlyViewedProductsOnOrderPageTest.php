<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Scenario;

/**
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer.
 * 2. Create Product.
 * 3. Open Product on Frontend.
 *
 * Steps:
 * 1. Open Customers > All Customers.
 * 2. Search and open customer from preconditions.
 * 3. Click Create Order.
 * 4. Check product in Recently Viewed Products section.
 * 5. Click Update Changes.
 * 6. Click Configure.
 * 7. Fill data from dataSet.
 * 8. Click OK.
 * 9. Click Update Items and Qty's button.
 * 10. Perform all assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-29723
 */
class MoveRecentlyViewedProductsOnOrderPageTest extends Scenario
{
    /**
     * Runs Move Recently Viewed Products On Order Page.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }
}
