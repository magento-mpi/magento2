<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

/**
 * Test Creation for CreateOrderFromCustomerPage (RecentlyComparedProducts)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create products
 * 3. Add products to compare list
 * 4. Clear compare list
 *
 * Steps:
 * 1. Open Customers -> All Customers
 * 2. Search and open customer from preconditions
 * 3. Click 'Create Order'
 * 4. Check product in 'Recently compared List' section
 * 5. Click 'Update Changes'
 * 6. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28109
 */
class MoveRecentlyComparedProductsOnOrderPageTest extends AbstractMoveComparedProductsOnOrderPageTest
{
    /**
     * Move recently compared products on order page.
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
        $this->removeProductsFromComparedList();

        // Steps:
        $this->openCustomerPageAndClickCreateOrder();
        $activitiesBlock = $this->orderCreateIndex->getCustomerActivitiesBlock();
        $activitiesBlock->getRecentlyComparedProductsBlock()->addToOrderByName($this->extractProductNames($products));
        $activitiesBlock->updateChanges();

        return ['products' => $products, 'productsIsConfigured' => false];
    }

    /**
     * Remove products from compare list
     *
     * @return void
     */
    protected function removeProductsFromComparedList()
    {
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        $this->catalogProductCompare->getCompareProductsBlock()->removeAllProducts();
    }
}
