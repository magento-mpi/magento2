<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\Constraint;

use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertMaxRequestedQtyFailMessage
 * Assert that after adding products by sku to cart, requested quantity is more than allowed error message appears
 */
class AssertMaxRequestedQtyFailMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Error requested quantity message
     */
    const ERROR_QUANTITY_MESSAGE = 'The product cannot be added to cart in requested quantity.';

    /**
     * Error maximum quantity allowed message
     */
    const ERROR_MAXIMUM_QUANTITY_MESSAGE = 'The maximum quantity allowed for purchase is %d.';

    /**
     * Assert that requested quantity is more than allowed error message is displayed after adding products to cart
     *
     * @param CheckoutCart $checkoutCart
     * @param array $requiredAttentionProducts
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $requiredAttentionProducts)
    {
        foreach ($requiredAttentionProducts as $product) {
            $currentMessage = $checkoutCart->getAdvancedCheckoutCart()->getFailedItemErrorMessage($product);
            \PHPUnit_Framework_Assert::assertContains(
                self::ERROR_QUANTITY_MESSAGE,
                $currentMessage,
                'Wrong error message is displayed.'
            );
            \PHPUnit_Framework_Assert::assertContains(
                sprintf(self::ERROR_MAXIMUM_QUANTITY_MESSAGE, $product->getData('stock_data')['max_sale_qty']),
                $currentMessage,
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
        return 'Requested quantity is more than allowed error message is present after adding products to cart.';
    }
}
