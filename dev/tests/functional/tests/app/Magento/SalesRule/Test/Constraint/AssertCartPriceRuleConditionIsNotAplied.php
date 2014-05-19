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
 * Class AssertCartPriceRuleConditionIsNotAplied
 *
 */
class AssertCartPriceRuleConditionIsNotAplied extends AbstractConstraint
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
        $grandTotal = $checkoutCart->getTotalsBlock()->getGrandTotal();
        if($checkoutCart->getTotalsBlock()->isVisibleShippingPriceBlock()){
            preg_match('/\$(.*)$/',$grandTotal, $grandTotalMatch);
            preg_match('/\$(.*)$/',$checkoutCart->getTotalsBlock()->getChippingPrice(), $shippingPriceMatch);
            $grandTotal = number_format(($grandTotalMatch[1] - $shippingPriceMatch[1]), 2);
            $grandTotal = '$'.$grandTotal;
        }
        \PHPUnit_Framework_Assert::assertEquals(
            $checkoutCart->getTotalsBlock()->getSubtotal(),
            $grandTotal,
            'Shopping cart subtotal: \'' .$checkoutCart->getTotalsBlock()->getSubtotal()
            . '\' not equals with grand total without shipping price: \''. $grandTotal . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shopping cart subtotal equals with grand total.';
    }
}
