<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
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
 * 2. Create Product
 * 3. Create Order with this product
 *
 * Steps:
 * 1. Open Customers ->All Customers
 * 2. Search and open customer from preconditions
 * 3. Click Create Order
 * 4. Check product in Last Ordered Items section
 * 5. Click Update Changes
 * 6. Perform all assertions
 *
 * @group Customers_(CS), Order_Management_(CS)
 * @ZephyrId MTA-352
 */
class CreateOrderFromCustomerPageTest extends Injectable
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
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data
     *
     * @param CustomerInjectable $customer
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        CustomerInjectable $customer,
        FixtureFactory $fixtureFactory
    ) {
        $customer->persist();
        $this->fixtureFactory = $fixtureFactory;

        return ['customer' => $customer];
    }

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
     * Run test
     *
     * @param OrderInjectable $order
     * @param OrderInjectable $orderInitial
     * @param CustomerInjectable $customer
     * @return array
     */
    public function test(
        OrderInjectable $order,
        OrderInjectable $orderInitial,
        CustomerInjectable $customer
    ) {
        // Preconditions:
        $order = $this->createOrder($order, $orderInitial, $customer);

        // Steps:
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
        $products = $this->prepareProductsData($order->getEntityId());
        $this->orderCreateIndex->getCustomerActivitiesBlock()->getLastOrderedItemsBlock()->addToOrderByName($products);
        $this->orderCreateIndex->getCustomerActivitiesBlock()->updateChanges();

        return ['entityData' => $order->getEntityId()];
    }

    /**
     * Create order via curl
     *
     * @param OrderInjectable $order
     * @param OrderInjectable $orderInitial
     * @param CustomerInjectable $customer
     * @return OrderInjectable
     */
    protected function createOrder(OrderInjectable $order, OrderInjectable $orderInitial, CustomerInjectable $customer)
    {
        $data = $orderInitial->getData();
        $data['billing_address_id'] = ['value' => $data['billing_address_id']];
        $data['entity_id'] = ['value' => $order->getEntityId()];
        $data['customer_id'] = ['customer' => $customer];
        $order = $this->fixtureFactory->createByCode('orderInjectable', ['data' => $data]);
        $order->persist();

        return $order;
    }

    /**
     * Prepare products data
     *
     * @param array|null $data
     * @throws \Exception
     * @return array
     */
    protected function prepareProductsData($data)
    {
        $result = [];
        if ($data === null || !isset($data['data'])) {
            throw new \Exception("In this order no products");
        }
        foreach ($data['data'] as $item) {
            $result[] = $item['name'];
        }
        return $result;
    }
}
