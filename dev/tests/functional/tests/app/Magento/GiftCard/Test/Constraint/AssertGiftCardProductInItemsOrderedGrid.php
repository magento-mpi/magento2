<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\GiftCard\Test\Fixture\GiftCardProduct;
use Magento\Sales\Test\Constraint\AssertProductInItemsOrderedGrid;

/**
 * Class AssertGiftCardProductInItemsOrderedGrid
 * Assert gift card product was added to Items Ordered grid in customer account on Order creation page backend
 */
class AssertGiftCardProductInItemsOrderedGrid extends AssertProductInItemsOrderedGrid
{
    /**
     * Get gift card product price
     *
     * @param FixtureInterface $product
     * @throws \Exception
     * @return int
     */
    protected function getProductPrice(FixtureInterface $product)
    {
        if (!$product instanceof GiftCardProduct) {
            throw new \Exception("Product '{$product->getName()}' is not gift card product.");
        }
        $checkoutData = $product->getCheckoutData();
        if ($checkoutData === null) {
            return 0;
        }

        return $checkoutData['cartItem']['price'];
    }
}
