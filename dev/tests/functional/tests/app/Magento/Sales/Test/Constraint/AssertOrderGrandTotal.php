<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\SalesOrderView;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderGrandTotal
 * Assert that Order Grand Total is correct on order page in backend
 */
class AssertOrderGrandTotal extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Order Grand Total is correct on order page in backend
     *
     * @param SalesOrderView $salesOrderView
     * @param string $grandTotal
     * @return void
     */
    public function processAssert(SalesOrderView $salesOrderView, $grandTotal)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $grandTotal,
            $salesOrderView->getOrderTotalsBlock()->getGrandTotal(),
            'Grand Total price does not equal to price from data set.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Grand Total price equals to price from data set.';
    }
}
