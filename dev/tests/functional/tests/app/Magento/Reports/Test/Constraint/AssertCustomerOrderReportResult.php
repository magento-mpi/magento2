<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertCustomerOrderReportResult
 * Check Order report grid for all params
 */
abstract class AssertCustomerOrderReportResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Prepare filter
     *
     * @param CustomerInjectable $customer
     * @param array $columns
     * @param array $report
     * @return array
     */
    public function prepareFilter(CustomerInjectable $customer, array $columns, array $report)
    {
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

        return $filter = [
            'date' => date($format),
            'customer' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'orders' => $columns['orders'],
            'average' => number_format($columns['average'], 2),
            'total' => number_format($columns['total'], 2)
        ];
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Order is present in reports grid.';
    }
}
