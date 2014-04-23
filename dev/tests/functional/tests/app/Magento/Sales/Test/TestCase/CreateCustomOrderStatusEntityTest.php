<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Sales\Test\Fixture\OrderStatus;
use Magento\Sales\Test\Page\Adminhtml\OrderStatusIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderStatusNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Custom Order Status Entity
 *
 * Test Flow:
 * 1. Log in as admin
 * 2. Navigate to the Stores>Settings>Order Status
 * 3. Click on "Create New Status" button
 * 4. Fill in all data according to data set
 * 5. Save order status
 * 6. Verify created order status
 *
 * @group Order_Management_(CS)
 * @ZephyrId MTA-10
 */
class CreateCustomOrderStatusEntityTest extends Injectable
{
    /**
     * @var OrderStatusIndex
     */
    protected $orderStatusIndexPage;

    /**
     * @var OrderStatusNew
     */
    protected $orderStatusNewPage;

    /**
     * @param OrderStatusIndex $orderStatusIndexPage
     * @param OrderStatusNew $orderStatusNewPage
     */
    public function __inject(
        OrderStatusIndex $orderStatusIndexPage,
        OrderStatusNew $orderStatusNewPage
    ) {
        $this->orderStatusIndexPage = $orderStatusIndexPage;
        $this->orderStatusNewPage = $orderStatusNewPage;
    }

    /**
     * @param OrderStatus $orderStatus
     */
    public function testCreateOrderStatus(
        OrderStatus $orderStatus
    ) {
        // Steps
        $this->orderStatusIndexPage->open();

        $this->orderStatusIndexPage->getGridPageActions()->addNew();
        $this->orderStatusNewPage->getOrderStatusForm()->fill($orderStatus);
        $this->orderStatusNewPage->getFormPageActions()->save();
    }
}
