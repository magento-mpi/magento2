<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Sales\Test\Constraint\AssertOrderStatusSuccessAssignMessage;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Fixture\OrderStatus;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderStatusIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderStatusAssign;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for AssignCustomOrderStatus
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Custom Order Status is created
 *
 * Steps:
 * 1. Log in as admin
 * 2. Navigate to the Stores > Settings > Order Status
 * 3. Click on "Assign Status to State
 * 4. Fill in all data according to data set
 * 5. Save Status Assignment
 * 6. Call assert assertOrderStatusSuccessAssignMessage
 * 7. Create Order
 * 8. Perform all assertions from dataSet
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-29382
 */
class AssignCustomOrderStatusTest extends Injectable
{
    /**
     * Order Status Index page
     *
     * @var OrderStatusIndex
     */
    protected $orderStatusIndex;

    /**
     * Order Status Assign page
     *
     * @var OrderStatusAssign
     */
    protected $orderStatusAssign;

    /**
     * Order Index page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Customer Account Logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * OrderStatus Fixture
     *
     * @var OrderStatus
     */
    protected $orderStatus;

    /**
     * OrderInjectable Fixture
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Injection data
     *
     * @param OrderStatusIndex $orderStatusIndex
     * @param OrderStatusAssign $orderStatusAssign
     * @param CustomerAccountLogout $customerAccountLogout
     * @param OrderIndex $orderIndex
     * @return void
     */
    public function __inject(
        OrderStatusIndex $orderStatusIndex,
        OrderStatusAssign $orderStatusAssign,
        CustomerAccountLogout $customerAccountLogout,
        OrderIndex $orderIndex
    ) {
        $this->orderStatusIndex = $orderStatusIndex;
        $this->orderStatusAssign = $orderStatusAssign;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->orderIndex = $orderIndex;
    }

    /**
     * Run Assign Custom OrderStatus test
     *
     * @param OrderStatus $initialOrderStatus
     * @param OrderInjectable $order
     * @param array $orderStatusState
     * @param AssertOrderStatusSuccessAssignMessage $assertion
     * @return array
     */
    public function test(
        OrderStatus $initialOrderStatus,
        OrderInjectable $order,
        array $orderStatusState,
        AssertOrderStatusSuccessAssignMessage $assertion
    ) {
        // Preconditions:
        $initialOrderStatus->persist();
        /** @var OrderStatus $orderStatus */
        $orderStatus = $this->fixtureFactory->createByCode(
            'orderStatus',
            ['data' => array_merge($initialOrderStatus->getData(), $orderStatusState)]
        );

        // Steps:
        $this->orderStatusIndex->open();
        $this->orderStatusIndex->getGridPageActions()->assignStatusToState();
        $this->orderStatusAssign->getAssignForm()->fill($orderStatus);
        $this->orderStatusAssign->getPageActionsBlock()->save();
        $assertion->processAssert($this->orderStatusIndex);

        $order->persist();

        // Prepare data for tear down
        $this->orderStatus = $orderStatus;
        $this->order = $order;

        return [
            'orderId' => $order->getId(),
            'customer' => $order->getDataFieldConfig('customer_id')['source']->getCustomer(),
            'orderStatus' => $orderStatus->getLabel()
        ];
    }

    /**
     * Change created order status and unassign custom order status
     *
     * @return void
     */
    public function tearDown()
    {
        $this->orderIndex->open()->getSalesOrderGrid()->massaction([['id' => $this->order->getId()]], 'Cancel');
        $filter = ['label' => $this->orderStatus->getLabel()];
        $this->orderStatusIndex->open()->getOrderStatusGrid()->searchAndUnassign($filter);
    }
}
