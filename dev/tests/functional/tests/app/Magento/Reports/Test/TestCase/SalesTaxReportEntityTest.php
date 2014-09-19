<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Reports\Test\Page\Adminhtml\Statistics;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Reports\Test\Page\Adminhtml\SalesTaxReport;

/**
 * Test Creation for SalesTaxReportEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Product created
 * 2. Customer created
 * 3. Tax Rule created
 * 4. Order placed
 * 5. Refresh statistic
 *
 * Steps:
 * 1. Login to backend
 * 2. Go to Reports> Sales > Tax
 * 3. Fill data from dataSet
 * 4. Click "Show report"
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-28515
 */
class SalesTaxReportEntityTest extends Injectable
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
     * Sales tax report page
     *
     * @var SalesTaxReport
     */
    protected $salesTaxReport;

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
     * Tax Rule grid page
     *
     * @var TaxRuleIndex
     */
    protected $taxRuleIndexPage;

    /**
     * Tax Rule new and edit page
     *
     * @var TaxRuleNew
     */
    protected $taxRuleNewPage;

    /**
     * Tax Rule fiwture
     *
     * @var TaxRule
     */
    protected $taxRule;

    /**
     * Injection data
     *
     * @param OrderIndex $orderIndex
     * @param OrderInvoiceNew $orderInvoiceNew
     * @param OrderView $orderView
     * @param Statistics $reportStatistic
     * @param SalesTaxReport $salesTaxReport
     * @param TaxRuleIndex $taxRuleIndexPage
     * @param TaxRuleNew $taxRuleNewPage
     * @return void
     */
    public function __inject(
        OrderIndex $orderIndex,
        OrderInvoiceNew $orderInvoiceNew,
        OrderView $orderView,
        Statistics $reportStatistic,
        SalesTaxReport $salesTaxReport,
        TaxRuleIndex $taxRuleIndexPage,
        TaxRuleNew $taxRuleNewPage
    ) {
        $this->orderIndex = $orderIndex;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->orderView = $orderView;
        $this->reportStatistic = $reportStatistic;
        $this->salesTaxReport = $salesTaxReport;
        $this->taxRuleIndexPage = $taxRuleIndexPage;
        $this->taxRuleNewPage = $taxRuleNewPage;
    }

    /**
     * @param CustomerInjectable $customer
     * @param string $order
     * @param TaxRule $taxRule
     * @param array $report
     * @param FixtureFactory $fixtureFactory
     * @param string $orderStatus
     * @param string $invoice
     * @return array
     */
    public function test(
        CustomerInjectable $customer,
        $order,
        TaxRule $taxRule,
        array $report,
        FixtureFactory $fixtureFactory,
        $orderStatus,
        $invoice
    ) {
        // Precondition
        $customer->persist();
        $taxRule->persist();
        $this->taxRule = $taxRule;
        $order = $fixtureFactory->createByCode(
            'orderInjectable',
            ['dataSet' => $order, 'data' => ['customer_id' => ['customer' => $customer]]]
        );
        $order->persist();
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        if ($orderStatus === 'Processing') {
            $this->orderView->getPageActions()->invoice();
            $this->orderInvoiceNew->getTotalsBlock()->submit();
        } elseif ($orderStatus === 'Complete') {
            $this->orderView->getPageActions()->invoice();
            $this->orderInvoiceNew->getCreateBlock()->fill($invoice, $order->getEntityId()['products']);
            $this->orderInvoiceNew->getTotalsBlock()->submit();
        }
        $this->reportStatistic->open();
        $this->reportStatistic->getGridBlock()->massaction(
            [['report' => 'Tax']],
            'Refresh Statistics for the Last Day',
            true
        );

        // Steps
        $this->salesTaxReport->open();
        $this->salesTaxReport->getFilterBlock()->viewsReport($report);
        $this->salesTaxReport->getActionBlock()->showReport();

        return ['order' => $order, 'taxRule' => $taxRule];
    }

    /**
     * Log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $filters = [
            'code' => $this->taxRule->getCode(),
        ];
        $this->taxRuleIndexPage->open();
        $this->taxRuleIndexPage->getTaxRuleGrid()->searchAndOpen($filters);
        $this->taxRuleNewPage->getFormPageActions()->delete();
    }
}
