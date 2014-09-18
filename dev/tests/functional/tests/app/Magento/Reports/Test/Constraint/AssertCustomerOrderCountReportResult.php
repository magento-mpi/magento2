<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\CustomerOrdersReport;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertCustomerOrderCountReportResult
 * Assert OrderCountReport grid for all params
 */
class AssertCustomerOrderCountReportResult extends AssertCustomerOrderTotalReportResult
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert OrderCountReport grid for all params
     *
     * @param CustomerOrdersReport $customerOrdersReport
     * @param CustomerInjectable $customer
     * @param array $columns
     * @param array $report
     * @return void
     */
    public function processAssert(
        CustomerOrdersReport $customerOrdersReport,
        CustomerInjectable $customer,
        array $columns,
        array $report
    ) {
        $filter = $this->prepareFilter($customer, $columns, $report);

        \PHPUnit_Framework_Assert::assertTrue(
            $customerOrdersReport->getGridBlock()->isRowVisible($filter),
            'Order does not present in count grid.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Order count is present in count grid.';
    }
}
