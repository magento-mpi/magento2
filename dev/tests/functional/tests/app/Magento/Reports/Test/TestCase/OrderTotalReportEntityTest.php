<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Reports\Test\Page\Adminhtml\OrderTotalReport;

/**
 * Test Creation for OrderTotalReportEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create orders for customer
 *
 * Steps:
 * 1. Login to backend
 * 2. Open Reports > Customer > Order Total
 * 3. Fill data from dataSet
 * 4. Click button Refresh
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-28358
 */
class OrderTotalReportEntityTest extends Injectable
{
    /**
     * Order total report page
     *
     * @var OrderTotalReport
     */
    protected $orderTotalReport;

    /**
     * Inject page
     *
     * @param OrderTotalReport $orderTotalReport
     * @return void
     */
    public function __inject(OrderTotalReport $orderTotalReport)
    {
        $this->orderTotalReport = $orderTotalReport;
    }

    /**
     * Order total report view
     *
     * @param CustomerInjectable $customer
     * @param string $orders
     * @param array $report
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function test(CustomerInjectable $customer, $orders, array $report, FixtureFactory $fixtureFactory)
    {
        // Precondition
        $customer->persist();
        $orders = explode(',', $orders);
        foreach ($orders as $order) {
            $order = $fixtureFactory->createByCode(
                'orderInjectable',
                ['dataSet' => $order, 'data' => ['customer_id' => ['customer' => $customer]]]
            );
            $order->persist();
        }

        // Steps
        $this->orderTotalReport->open();
        $this->orderTotalReport->getFilterBlock()->viewsReport($report);
        $this->orderTotalReport->getFilterBlock()->refreshFilter();
    }
}
