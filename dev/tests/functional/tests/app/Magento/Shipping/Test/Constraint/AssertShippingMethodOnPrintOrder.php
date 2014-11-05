<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Constraint;

use Magento\Sales\Test\Page\SalesGuestPrint;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertShippingMethodOnPrintOrder
 * Assert that shipping method was printed correctly on sales guest print page.
 */
class AssertShippingMethodOnPrintOrder extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that shipping method was printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param string $shippingMethod
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, $shippingMethod)
    {
        $shippingBlock = $salesGuestPrint->getPrintOrderShipping()->getShippingMethodBlock();
        \PHPUnit_Framework_Assert::assertTrue(
            $shippingBlock->isShippingMethodVisible($shippingMethod),
            "Shipping method was printed incorrect."
        );

    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Shipping method was printed correctly.";
    }
}
