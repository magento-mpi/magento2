<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertGiftCardDiscount
 * Assert that sum of gift card discount is equal to passed from dataSet in shopping cart
 */
class AssertGiftCardDiscount extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that sum of gift card discount is equal to passed from dataSet in shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @param string $discount
     * @return void
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        $discount
    ) {
        $checkoutCart->open();
        $actualDiscount = substr($checkoutCart->getGiftCardDiscountBlock()->getGiftCardDiscount(), 1);
        \PHPUnit_Framework_Assert::assertEquals(
            $discount,
            $actualDiscount,
            'Gift card discount price is not equal to the price from fixture.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card discount price is equal to the price from fixture.';
    }
}
