<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertProductIsOutOfStockFailMessage
 * Assert that after adding products by sku to cart, out of stock error message appears
 */
class AssertProductIsOutOfStockFailMessage extends AbstractConstraint
{
    /**
     * Out of stock error message
     */
    const ERROR_MESSAGE = 'Availability: Out of stock.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that out of stock error message is displayed after adding products to cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, $products)
    {
        foreach ($products as $product) {
            \PHPUnit_Framework_Assert::assertContains(
                self::ERROR_MESSAGE,
                $checkoutCart->getCartBlock()->getFailedItemErrorMessage($product),
                'Wrong error message is displayed.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Out of stock error message is present after adding products to cart by sku.';
    }
}
