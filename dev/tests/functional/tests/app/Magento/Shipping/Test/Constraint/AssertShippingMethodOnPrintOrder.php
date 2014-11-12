<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesGuestPrint;

/**
 * Assert that shipping method was printed correctly on sales guest print page.
 */
class AssertShippingMethodOnPrintOrder extends AbstractConstraint
{
    /**
     * Shipping method and carrier template.
     */
    const SHIPPING_TEMPLATE = "%s - %s";

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
     * @param array $shipping
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, $shipping)
    {
        $expected = sprintf(self::SHIPPING_TEMPLATE, $shipping['shipping_service'], $shipping['shipping_method']);
        \PHPUnit_Framework_Assert::assertTrue(
            $salesGuestPrint->getInfoShipping()->isShippingMethodVisible($expected),
            "Shipping method was printed incorrectly."
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
