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
 * Class AssertInvoiceReportTotalResult
 * Assert that total invoice info in report grid is actual
 */
class AssertInvoiceReportTotalResult extends AbstractAssertInvoiceReportResult
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that total invoice info in report grid is actual
     *
     * @param OrderInjectable $order
     * @param array $invoiceReport
     * @param array $initialInvoiceTotalResult
     * @return void
     */
    public function processAssert(OrderInjectable $order, array $invoiceReport, array $initialInvoiceTotalResult)
    {
        $this->order = $order;
        $this->searchInInvoiceReportGrid($invoiceReport);
        $invoiceResult = $this->salesInvoiceReport->getGridBlock()->getTotalResult();
        $prepareInitialResult = $this->prepareExpectedResult($initialInvoiceTotalResult);
        \PHPUnit_Framework_Assert::assertEquals(
            $prepareInitialResult,
            $invoiceResult,
            "Grand total Invoice result is not correct."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoice report grand total result contains actual data.';
    }
}
