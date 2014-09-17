<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\CustomerOrdersReport;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertOrderCountReportInGrid
 * Assert OrderCountReport grid for all params
 */
class AssertOrderCountReportInGrid extends AbstractConstraint
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
        $format = '';
        switch ($report['report_period']) {
            case 'Day':
                $format = 'M j, Y';
                break;
            case 'Month':
                $format = 'j/Y';
                break;
            case 'Year':
                $format = 'Y';
                break;
        }
        $filter = [
            'date' => date($format),
            'customer' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'orders' => $columns['orders'],
            'average' => number_format($columns['average'], 2),
            'total' => number_format($columns['total'], 2)
        ];

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
        return 'Order total is present in count grid.';
    }
}

