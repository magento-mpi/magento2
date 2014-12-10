<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertSkuNotFoundFailMessage
 * Assert that after adding products by sku to shopping cart, sku not found error message appears
 */
class AssertSkuNotFoundFailMessage extends AbstractConstraint
{
    /**
     * Sku not found error message
     */
    const ERROR_MESSAGE = 'SKU not found in catalog.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that sku not found error message is displayed after adding products by sku to shopping cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            \PHPUnit_Framework_Assert::assertEquals(
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
        return 'Sku not found error message is present after adding products by sku to shopping cart.';
    }
}
