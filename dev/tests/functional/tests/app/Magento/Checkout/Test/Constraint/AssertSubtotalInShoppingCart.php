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
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertSubtotalInShoppingCart
 */
class AssertSubtotalInShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that subtotal total in the shopping cart is equals to expected total from data set
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        Cart $cart,
        CatalogProductSimple $product
    ) {
        $checkoutCart->open();
        $cartProductSubtotal = $checkoutCart->getCartBlock()->getCartItem($product)->getSubtotalPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $cartProductSubtotal,
            $cart->getRowTotal(),
            'Shopping cart subtotal: \'' . $cartProductSubtotal
            . '\' not equals with total from data set: \'' . $cart->getRowTotal() . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Subtotal in the shopping cart equals to expected total from data set.';
    }
}
