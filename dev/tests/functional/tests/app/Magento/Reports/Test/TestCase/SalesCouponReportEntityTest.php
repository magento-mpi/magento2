<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Reports\Test\Page\Adminhtml\Statistics;
use Magento\Reports\Test\Page\Adminhtml\SalesCouponReportView;

/**
 * Test Creation for SalesCouponReportEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create order with coupon
 * 2. Make invoice for this order
 * 3. Refresh statistic
 *
 * Steps:
 * 1. Login to backend
 * 2. Go to Reports > Sales > Coupons
 * 3. Select time range, report period etc
 * 4. Click "Show report"
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-28190
 */
class SalesCouponReportEntityTest extends Injectable
{
    /**
     * Order index page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * Order invoice new page
     *
     * @var OrderInvoiceNew
     */
    protected $orderInvoiceNew;

    /**
     * Sales coupon report view page
     *
     * @var SalesCouponReportView
     */
    protected $salesCouponReportView;

    /**
     * Order view page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * Report statistic page
     *
     * @var Statistics
     */
    protected $reportStatistic;

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param OrderInvoiceNew $orderInvoiceNew
     * @param SalesCouponReportView $salesCouponReportView
     * @param OrderView $orderView
     * @param Statistics $reportStatistic
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        OrderInvoiceNew $orderInvoiceNew,
        SalesCouponReportView $salesCouponReportView,
        OrderView $orderView,
        Statistics $reportStatistic
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->salesCouponReportView = $salesCouponReportView;
        $this->orderView = $orderView;
        $this->reportStatistic = $reportStatistic;
    }

    /**
     * Sales coupon report
     *
     * @param OrderInjectable $order
     * @param array $viewsReport
     * @return void
     */
    public function test(OrderInjectable $order, array $viewsReport)
    {
        // Precondition
        $order->persist();
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $this->orderView->getPageActions()->invoice();
        $this->orderInvoiceNew->getTotalsBlock()->submit();
        $this->reportStatistic->open();
        $this->reportStatistic->getGridBlock()->massaction(
            [['report' => 'Coupons']],
            'Refresh Statistics for the Last Day',
            true
        );

        // Steps
        $this->salesCouponReportView->open();
        $ruleName = $order->getCouponCode()->getName();
        $viewsReport['rules_list'] = str_replace('%rule_name%', $ruleName, $viewsReport['rules_list']);
        $this->salesCouponReportView->getFilterBlock()->viewsReport($viewsReport);
        $this->salesCouponReportView->getActionBlock()->showReport();
    }
}
