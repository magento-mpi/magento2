<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

/**
 * Class AssertCartPriceRuleConditionIsApplied
 * Check that shopping cart subtotal not equals with grand total(excluding shipping price if exist).
 */
class AssertCartPriceRuleConditionIsApplied extends assertCartPriceRuleApplying
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that shopping cart subtotal not equals with grand total
     *
     * @return void
     */
    protected function assert()
    {
        $this->checkoutCart->open();
        preg_match('/\$(.*)$/', $this->checkoutCart->getTotalsBlock()->getSubtotal(), $subTotalMatch);
        $subTotal = $subTotalMatch[1];
        preg_match('/\$(.*)$/', $this->checkoutCart->getTotalsBlock()->getGrandTotal(), $grandTotalMatch);
        $grandTotal = $grandTotalMatch[1];

        if ($this->checkoutCart->getTotalsBlock()->isVisibleShippingPriceBlock()) {
            preg_match('/\$(.*)$/', $this->checkoutCart->getTotalsBlock()->getChippingPrice(), $shippingPriceMatch);
            $grandTotal = number_format(($grandTotal - $shippingPriceMatch[1]), 2);
        }
        \PHPUnit_Framework_Assert::assertNotEquals(
            $subTotal,
            $grandTotal,
            'Shopping cart subtotal: \'' . $subTotal . '\' equals with grand total: \'' . $grandTotal . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shopping cart subtotal not equals with grand total - price rule condition is applied.';
    }
}
