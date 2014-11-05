<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test Creation for CreateOrderFromCustomerPage (comparedProducts)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create products
 * 3. Add products to compare list
 *
 * Steps:
 * 1. Open Customers -> All Customers
 * 2. Search and open customer from preconditions
 * 3. Click 'Create Order'
 * 4. Check product in comparison list section
 * 5. Click 'Update Changes'
 * 6. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28050
 */
class MoveProductsInComparedOnOrderPageTest extends AbstractMoveComparedProductsOnOrderPageTest
{
    /**
     * Move compare products on order page.
     *
     * @param string $products
     * @return array
     */
    public function test($products)
    {
        // Preconditions
        $products = $this->createProducts($products);
        $this->loginCustomer();
        $this->addProducts($products);

        // Steps
        $this->openCustomerPageAndClickCreateOrder();
        $activitiesBlock = $this->orderCreateIndex->getCustomerActivitiesBlock();
        $activitiesBlock->getProductsInComparisonBlock()->addToOrderByName($this->extractProductNames($products));
        $activitiesBlock->updateChanges();

        return ['products' => $products, 'productsIsConfigured' => false];
    }
}
