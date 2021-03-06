<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductRequiredAttentionFailMessage
 * Assert that after adding products by sku to shopping cart, product requires attention error message appears
 */
class AssertProductRequiredAttentionFailMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Product requires attention error message
     */
    const ERROR_MESSAGE = '%d %s requires your attention.';

    /**
     * Assert that product requires attention error message is displayed after adding products by sku to shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        $productsQty = count($requiredAttentionProducts);
        $qtyString = ($productsQty > 1) ? 'products' : 'product';
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::ERROR_MESSAGE, $productsQty, $qtyString),
            $checkoutCart->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product requires attention error message is present.';
    }
}
