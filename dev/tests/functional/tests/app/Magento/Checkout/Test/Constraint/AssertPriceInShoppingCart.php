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
 * Class AssertPriceInShoppingCart
 */
class AssertPriceInShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that price in the shopping cart equals to expected price from data set
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
        preg_match(
            '/\$(.*)$/',
            $checkoutCart->open()->getCartBlock()->getProductPriceByName($product->getName()),
            $cartProductPriceMatch
        );
        \PHPUnit_Framework_Assert::assertEquals(
            $cartProductPriceMatch[1],
            $cart->getPrice(),
            'Shopping cart product price: \'' . $cartProductPriceMatch[1]
            . '\' not equals with price from data set: \'' . $cart->getPrice() . '\''
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Price in the shopping cart equals to expected price from data set.';
    }
}
