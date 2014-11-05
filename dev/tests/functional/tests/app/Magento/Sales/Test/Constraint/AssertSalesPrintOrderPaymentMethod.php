<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesGuestPrint;

/**
 * Class AssertSalesPrintOrderPaymentMethod
 * Assert that payment method was printed correctly on sales guest print page.
 */
class AssertSalesPrintOrderPaymentMethod extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';


    /**
     * Assert that payment method was printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param string $paymentMethod
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, $paymentMethod)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $salesGuestPrint->getPrintOrder()->getPaymentMethodBlock()->isPaymentMethodVisible($paymentMethod),
            "Payment method was printed incorrect."
        );

    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Payment method was printed correctly.";
    }
}
