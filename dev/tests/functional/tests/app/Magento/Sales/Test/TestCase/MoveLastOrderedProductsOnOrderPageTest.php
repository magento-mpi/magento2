<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Test Creation for CreateOrderFromCustomerPage (lastOrder)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create product
 * 3. Create order with this product
 *
 * Steps:
 * 1. Open Customers -> All Customers
 * 2. Search and open customer from preconditions
 * 3. Click Create Order
 * 4. Check product in Last Ordered Items section
 * 5. Click Update Changes
 * 6. Perform all assertions
 *
 * @group Customers_(CS), Order_Management_(CS)
 * @ZephyrId MAGETWO-27640
 */
class MoveLastOrderedProductsOnOrderPageTest extends Injectable
{
    /**
     * Order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Customer index page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * Customer index edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * Injection data
     *
     * @param OrderCreateIndex $orderCreateIndex
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @return void
     */
    public function __inject(
        OrderCreateIndex $orderCreateIndex,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit
    ) {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
    }

    /**
     * Move last ordered products on order page
     *
     * @param OrderInjectable $order
     * @return array
     */
    public function test(OrderInjectable $order)
    {
        // Preconditions:
        $order->persist();
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();

        // Steps:
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
        $this->orderCreateIndex->getStoreBlock()->selectStoreView();
        $products = $this->extractProductNames($order->getEntityId());
        $this->orderCreateIndex->getCustomerActivitiesBlock()->getLastOrderedItemsBlock()->addToOrderByName($products);
        $this->orderCreateIndex->getCustomerActivitiesBlock()->updateChanges();

        return ['entityData' => $order->getEntityId()];
    }

    /**
     * Extract products name
     *
     * @param array $data
     * @return array
     */
    protected function extractProductNames($data)
    {
        $result = [];
        foreach ($data['products'] as $product) {
            $result[] = $product->getName();
        }
        return $result;
    }
}
