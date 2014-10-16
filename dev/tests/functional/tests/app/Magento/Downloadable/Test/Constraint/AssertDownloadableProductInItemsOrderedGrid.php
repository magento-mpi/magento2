<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;
use Magento\Sales\Test\Constraint\AssertProductInItemsOrderedGrid;

/**
 * Class AssertDownloadableProductInItemsOrderedGrid
 * Assert downloadable product was added to Items Ordered grid in customer account on Order creation page backend
 */
class AssertDownloadableProductInItemsOrderedGrid extends AssertProductInItemsOrderedGrid
{
    /**
     * Get downloadable product price
     *
     * @param FixtureInterface $product
     * @throws \Exception
     * @return int
     */
    protected function getProductPrice(FixtureInterface $product)
    {
        if (!$product instanceof DownloadableProductInjectable) {
            throw new \Exception("Product '{$product->getName()}' is not downloadable product.");
        }
        $checkoutData = $product->getCheckoutData();
        if ($checkoutData === null) {
            return 0;
        }

        return $checkoutData['cartItem']['price'];
    }
}
