<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductWithPaymentAgreementsOrderFailMessage
 * Assert that error message that product with payment agreements can't be ordered with other items in cart is displayed
 */
class AssertProductWithPaymentAgreementsOrderFailMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    // @codingStandardsIgnoreStart
    /**
     * Product with payment agreements can't be ordered with other items error message
     */
    const ERROR_MESSAGE = 'Sorry, but items with payment agreements must be ordered one at a time To continue, please remove or buy the other items in your cart, then order this item by itself.';
    // @codingStandardsIgnoreEnd

    /**
     * Assert that error message that product with payment agreements can't be ordered with other items is displayed
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            \PHPUnit_Framework_Assert::assertContains(
                self::ERROR_MESSAGE,
                $checkoutCart->getAdvancedCheckoutCart()->getFailedItemErrorMessage($product),
                'Wrong error message is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Product with payment agreements can't be ordered with other items error message is present.";
    }
}
