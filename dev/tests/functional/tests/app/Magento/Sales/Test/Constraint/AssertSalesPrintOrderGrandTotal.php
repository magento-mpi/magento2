<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\SalesGuestPrint;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that Grand Total price was printed correctly on sales guest print page.
 */
class AssertSalesPrintOrderGrandTotal extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Grand Total price was printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param string $grandTotal
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, $grandTotal)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $grandTotal,
            $salesGuestPrint->getPrintOrder()->getTotalsBlock()->getGrandTotal()
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Grand total was printed correctly.";
    }
}
