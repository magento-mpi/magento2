<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateOrderFromCustomerPage (RecentlyComparedProducts)
 *
 * Test Flow:
 *
 * @group Order_Management_(CS)
 * @ZephyrId MTA-395
 */
class MoveRecentlyComparedProductsOnOrderPageTest extends AbstractMoveComparedProductsOnOrderPageTest
{
    /**
     * Move recently compared products on order page
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
        $activitiesBlock->getProductsInComparisonBlock()->addToOrderByName($this->extractProductNames($products));
        $activitiesBlock->updateChanges();

        return ['entityData' => ['products' => $products], 'productsIsConfigured' => false];
    }
}
