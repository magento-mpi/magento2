<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

use Magento\Sales\Test\Page\SalesGuestPrint;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert that sales rule amount printed correctly on sales guest print page.
 */
class AssertSalesRuleOnPrintOrder extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that sales rule amount printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param string $salesRuleDiscount
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, $salesRuleDiscount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $salesRuleDiscount,
            $salesGuestPrint->getViewSalesRule()->getItemBlock()->getSalesRuleDiscount(),
            "Sales rule amount not equals."
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Sales rule amount was printed correctly.";
    }
}
