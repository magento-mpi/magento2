<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

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
     * @param $grandTotal
     * @return void
     */
    public function processAssert(SalesOrderView $salesOrderView, $grandTotal)
    {
        $orderReviewGrandTotal = $salesOrderView->getOrderTotalsBlock()->getGrandTotal();

        \PHPUnit_Framework_Assert::assertEquals(
            $orderReviewGrandTotal,
            $grandTotal,
            'Grand Total price: \'' . $orderReviewGrandTotal
            . '\' not equals with price from data set: \'' . $grandTotal . '\''
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Grand Total price equals with price from data set.';
    }
}
