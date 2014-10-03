<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Class AssertSalesReportTotalResult
 * Assert that total sales info in report grid is actual
 */
class AssertSalesReportTotalResult extends AbstractAssertSalesReportResult
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that total sales info in report grid is actual
     *
     * @param OrderInjectable $order
     * @param array $salesReport
     * @param array $initialSalesTotalResult
     * @return void
     */
    public function processAssert(OrderInjectable $order, array $salesReport, array $initialSalesTotalResult)
    {
        $this->order = $order;
        $this->searchInSalesReportGrid($salesReport);
        $salesResult = $this->salesReportPage->getGridBlock()->getSalesResults();
        $prepareInitialResult = $this->prepareExpectedResult($initialSalesTotalResult);
        \PHPUnit_Framework_Assert::assertEquals(
            $salesResult,
            $prepareInitialResult,
            "Grand total Sales result not correct."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales report grand total result contains actual data.';
    }
}
