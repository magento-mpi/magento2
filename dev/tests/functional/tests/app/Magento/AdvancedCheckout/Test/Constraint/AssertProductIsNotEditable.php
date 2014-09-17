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
 * Class AssertProductIsNotEditable
 * Assert that after adding products by sku to shopping cart products are not editable
 */
class AssertProductIsNotEditable extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after adding products by sku to shopping cart products are not editable
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, $products)
    {
        foreach ($products as $product) {
            \PHPUnit_Framework_Assert::assertTrue(
                $checkoutCart->getCartBlock()->getNotEditableCartItem($product)->isVisible(),
                'Added product is editable'
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
        return 'Added products by sku to shopping cart are not editable.';
    }
}
