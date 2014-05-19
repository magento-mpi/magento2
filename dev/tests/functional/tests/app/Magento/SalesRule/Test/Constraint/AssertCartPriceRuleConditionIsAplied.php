<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint; 

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertCartPriceRuleConditionIsAplied
 */
class AssertCartPriceRuleConditionIsAplied extends AbstractConstraint
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
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart)
    {
        $checkoutCart->open();
        \PHPUnit_Framework_Assert::assertNotEquals(
            $checkoutCart->getTotalsBlock()->getSubtotal(),
            $checkoutCart->getTotalsBlock()->getGrandTotal(),
            'Shopping cart subtotal: \'' .$checkoutCart->getTotalsBlock()->getSubtotal()
            . '\' equals with grand total: \''. $checkoutCart->getTotalsBlock()->getGrandTotal() . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shopping cart subtotal not equals with grand total.';
    }
}
