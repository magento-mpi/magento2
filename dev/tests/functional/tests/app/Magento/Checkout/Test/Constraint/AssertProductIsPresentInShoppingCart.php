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
 * Class AssertProductIsPresentInShoppingCart
 * Assert that products name in shopping cart are equal to expected
 */
class AssertProductIsPresentInShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert products name in shopping cart are equal to expected
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $products)
    {
        $checkoutCart->open();
        foreach ($products as $product) {
            $cartProductName = $checkoutCart->getCartBlock()->getCartItem($product)->getName();
            $productName = $product->getName();
            \PHPUnit_Framework_Assert::assertEquals(
                $cartProductName,
                $productName,
                'Shopping cart product name: \'' . $cartProductName
                . '\' not equals with name from data set: \'' . $productName . '\''
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
        return 'Products name in the shopping cart equals to expected name from data set.';
    }
}
