<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

/**
 * Class AssertCartPriceRuleConditionIsNotApplied
 * Check that shopping cart subtotal equals with grand total(excluding shipping price if exist).
 */
class AssertCartPriceRuleConditionIsNotApplied extends AssertCartPriceRuleApplying
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that shopping cart subtotal equals with grand total
     *
     * @return void
     */
    protected function assert()
    {
        $subTotal =  $this->checkoutCart->getTotalsBlock()->getSubtotal();
        $grandTotal =  $this->checkoutCart->getTotalsBlock()->getGrandTotal();

        if ($this->checkoutCart->getTotalsBlock()->isVisibleShippingPriceBlock()) {
            $shippingPrice = $this->checkoutCart->getTotalsBlock()->getShippingPrice();
            $grandTotal = number_format(($grandTotal - $shippingPrice), 2);
        }
        \PHPUnit_Framework_Assert::assertEquals(
            $subTotal,
            $grandTotal,
            'Shopping cart subtotal: \'' . $subTotal . '\' not equals with grand total: \'' . $grandTotal . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Shopping cart subtotal equals to grand total - price rule has not been applied.";
    }
}
