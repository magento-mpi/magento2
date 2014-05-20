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
 * Class AssertCartPriceRuleFreeShippingIsApplied
 */
class AssertCartPriceRuleFreeShippingIsApplied extends AbstractConstraint
{
    const FREE_CHIPPING_PRICE = '$0.00';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that free shipping is applied in shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart)
    {
        $currentChippingPrice = $checkoutCart->open()->getTotalsBlock()->getChippingPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $currentChippingPrice,
            self::FREE_CHIPPING_PRICE,
            'Current shipping price: \'' .$checkoutCart->getTotalsBlock()->getChippingPrice()
            . '\' not equals with free shipping price: \''. self::FREE_CHIPPING_PRICE . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Free shipping is applied.';
    }
}
