<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Assert that products are absent in shopping cart.
 */
class AssertProductsAbsentInShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that products are absent in shopping cart.
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $products)
    {
        $checkoutCart->open();
        foreach ($products as $product) {
            \PHPUnit_Framework_Assert::assertFalse(
                $checkoutCart->getCartBlock()->getCartItem($product)->isVisible(),
                'Product ' . $product->getName() . ' is present in shopping cart.'
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
        return 'All expected products are absent in shopping cart.';
    }
}
