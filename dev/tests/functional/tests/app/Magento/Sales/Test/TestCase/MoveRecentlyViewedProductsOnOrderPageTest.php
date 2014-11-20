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
 * 2. Create product.
 * 3. Open product on frontend.
 *
 * Steps:
 * 1. Login in to Backend.
 * 2. Open Customers > All Customers.
 * 3. Search and open customer from preconditions.
 * 4. Click Create Order.
 * 5. Check product in Recently Viewed Products section.
 * 6. Click Update Changes.
 * 7. Click Configure.
 * 8. Fill data from dataSet.
 * 9. Click OK.
 * 10. Click Update Items and Qty's button.
 * 11. Perform all assertions.
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
