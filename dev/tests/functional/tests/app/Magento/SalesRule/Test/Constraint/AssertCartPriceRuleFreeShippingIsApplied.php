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
 *
 * @package Magento\SalesRule\Test\Constraint
 */
class AssertCartPriceRuleFreeShippingIsApplied extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Free shipping price
     *
     * @var string
     */
    protected $freeChippingPrice = '$0.00';

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
            $this->freeChippingPrice,
            'Current shipping price: \'' .$checkoutCart->getTotalsBlock()->getChippingPrice()
            . '\' not equals with free shipping price: \''. $this->freeChippingPrice . '\''
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
