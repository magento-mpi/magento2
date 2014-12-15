<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

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
        $actualDiscount = $checkoutCart->getGiftCardDiscountBlock()->getGiftCardDiscount();
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
