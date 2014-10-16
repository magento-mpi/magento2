<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Bundle\Test\Fixture\BundleProduct;
use Magento\Sales\Test\Constraint\AssertProductInItemsOrderedGrid;

/**
 * Class AssertBundleProductInItemsOrderedGrid
 * Assert bundle product was added to Items Ordered grid in customer account on Order creation page backend
 */
class AssertBundleProductInItemsOrderedGrid extends AssertProductInItemsOrderedGrid
{
    /**
     * Get bundle product price
     *
     * @param FixtureInterface $product
     * @throws \Exception
     * @return int
     */
    protected function getProductPrice(FixtureInterface $product)
    {
        if (!$product instanceof BundleProduct) {
            throw new \Exception("Product '{$product->getName()}' is not bundle product.");
        }
        $checkoutData = $product->getCheckoutData();
        if ($checkoutData === null) {
            return 0;
        }

        return $checkoutData['cartItem']['price'];
    }
}
