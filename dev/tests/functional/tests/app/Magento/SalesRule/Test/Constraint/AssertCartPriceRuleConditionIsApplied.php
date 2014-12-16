<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\Constraint;

/**
 * Class AssertCartPriceRuleConditionIsApplied
 * Check that shopping cart subtotal not equals with grand total(excluding shipping price if exist).
 */
class AssertCartPriceRuleConditionIsApplied extends AssertCartPriceRuleApplying
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that shopping cart subtotal not equals with grand total
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
        return "Shopping cart subtotal doesn't equal to grand total - price rule has been applied.";
    }
}
