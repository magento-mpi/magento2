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
 * Class AssertCartProductName
 * Assert that product(s) name in shopping cart is/are equal to expected
 */
class AssertCartProductName extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product(s) name in shopping cart is/are equal to expected
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        array $products
    ) {
        $checkoutCart->open();
        foreach ($products as $product) {
            $cartProductName = $checkoutCart->getCartBlock()->getCartItem($product)->getName();
            \PHPUnit_Framework_Assert::assertEquals(
                $cartProductName,
                $product->getName(),
                'Shopping cart product name: \'' . $cartProductName
                . '\' not equals with name from data set: \'' . $product->getName() . '\''
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
        return 'Product(s) name in the shopping cart equals to expected name from data set.';
    }
}
